<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesProducts;
use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\LeadReminder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\LeadCreationService;
use App\Services\LeadIdGenerator;
use App\Services\LeadLogger;
use App\Services\LeadWorkflowService;
use App\Support\BusinessTypeMapper;
use App\Exports\MultisiteSitesTemplateExport;
use App\Support\LeadValidationRules;
use App\Support\MultisiteSitesCsv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    use AuthorizesRequests;
    use ScopesProducts;

    public function __construct(private LeadWorkflowService $workflow)
    {
    }

    /**
     * Scopes a leads query to what $user is allowed to see - the
     * same rule LeadPolicy::view() enforces for a single lead.
     * Admin/Super Admin see everything; MIS User additionally sees
     * every published lead (on top of leads they created
     * themselves); an Account Executive sees only the leads they
     * created; everyone else sees leads they created or that are
     * (or were) assigned to them as Account Manager.
     */
    private function scopeLeadsVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdminOrAbove()) {
            return $query;
        }

        if ($user->isMis()) {
            return $query->where(function (Builder $q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('status', '!=', Lead::STATUS_DRAFT);
            });
        }

        if ($user->isAe()) {
            return $query->where('created_by', $user->id);
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('assigned_to', $user->id)
                ->orWhere('account_manager_id', $user->id);
        });
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Lead::with(['product', 'assignee:id,name']);

            $user = Auth::user();

            $this->scopeLeadsVisibleTo($query, $user);

            // recordsTotal must reflect the base (role-scoped) set,
            // BEFORE search/status/product filters are applied.
            $recordsTotal = (clone $query)->count();

            if ($request->has('search') && !empty($request->search['value'])) {

                $search = $request->search['value'];

                $query->where(function ($q) use ($search) {

                    $q->where('company_business_name', 'like', "%{$search}%")
                        ->orWhere('company_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");

                    // Status is stored as "published" but shown as
                    // "Open" - so also match the label people see.
                    $matchingStatuses = collect(Lead::STATUS_LABELS)
                        ->filter(fn ($label) => str_contains(strtolower($label), strtolower($search)))
                        ->keys()
                        ->all();

                    if ($matchingStatuses) {
                        $q->orWhereIn('status', $matchingStatuses);
                    }

                });
            }

            /*
            |--------------------------------------------------------------------------
            | Toolbar filters - Status / Product
            |--------------------------------------------------------------------------
            |
            | Sent by the custom selects in the leads toolbar (#statusFilter,
            | #productFilter) via the ajax.data callback on the DataTable.
            | filled() is used instead of has() so an empty string ("All")
            | is correctly treated as "no filter".
            */
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            $recordsFiltered = (clone $query)->count();

            $columns = [
                0 => 'id',
                1 => 'product_id',
                2 => 'company_business_name',
                3 => 'company_number',
                4 => 'customer_name',
                5 => 'contact_person',
                6 => 'email',
                7 => 'status',
                8 => 'created_at',
            ];

            if ($request->has('order')) {

                $orderColumnIndex = $request->order[0]['column'] ?? 0;
                $orderDirection = $request->order[0]['dir'] ?? 'desc';

                if (isset($columns[$orderColumnIndex])) {

                    $query->orderBy(
                        $columns[$orderColumnIndex],
                        $orderDirection
                    );
                }

            } else {

                $query->latest();

            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $leads = $query
                ->skip($start)
                ->take($length)
                ->get();

            // Per-row Edit / Delete visibility, straight from
            // LeadPolicy, so the table never offers an action the
            // server would then reject.
            $leads->each(function (Lead $lead) use ($user) {
                $lead->setAttribute('can_edit', $user->can('update', $lead));
                $lead->setAttribute('can_delete', $user->can('delete', $lead));
            });

            return response()->json([
                'draw' => intval($request->draw),

                'recordsTotal' => $recordsTotal,

                'recordsFiltered' => $recordsFiltered,

                'data' => $leads,
            ]);
        }

        $user = Auth::user();

        $scopedLeads = $this->scopeLeadsVisibleTo(Lead::query(), $user);

        $totalLeadsCount = (clone $scopedLeads)->count();
        $draftLeadsCount = (clone $scopedLeads)->where('status', Lead::STATUS_DRAFT)->count();
        $publishedLeadsCount = (clone $scopedLeads)->where('status', Lead::STATUS_PUBLISHED)->count();
        // "Assigned" covers every live workflow stage (With Account
        // Manager, Pricing Declined, Hold, plus old-workflow AE stages)
        // - lost / closed leads are done, so they drop out of it.
        $assignedLeadsCount = (clone $scopedLeads)->whereIn('status', Lead::ACTIVE_WORKFLOW_STATUSES)->count();

        $productLeadCounts = (clone $scopedLeads)
            ->selectRaw('product_id, count(*) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        $products = $this->scopedProductsQuery($user)
            ->get()
            ->map(function ($product) use ($productLeadCounts) {
                $product->leads_count = $productLeadCounts[$product->id] ?? 0;
                return $product;
            });

        return view('leads.index', compact(
            'products',
            'totalLeadsCount',
            'draftLeadsCount',
            'publishedLeadsCount',
            'assignedLeadsCount'
        ));
    }

    /**
     * Total / Draft / Open (published) / Assigned counts, scoped the same way the
     * leads table itself is scoped for the current user. Shared by
     * index() (initial page load) and updateStatus() (so the stat
     * cards can be refreshed in place after an inline status change,
     * without a full page reload).
     */
    private function scopedLeadCounts($user): array
    {
        $scopedLeads = $this->scopeLeadsVisibleTo(Lead::query(), $user);

        return [
            'total' => (clone $scopedLeads)->count(),
            'draft' => (clone $scopedLeads)->where('status', Lead::STATUS_DRAFT)->count(),
            'published' => (clone $scopedLeads)->where('status', Lead::STATUS_PUBLISHED)->count(),
            'assigned' => (clone $scopedLeads)->whereIn('status', Lead::ACTIVE_WORKFLOW_STATUSES)->count(),
        ];
    }

    /**
     * Update only the status of a lead (used by the inline status
     * toggle on the leads listing page). Kept separate from
     * update() because that method requires the full validated
     * payload (product_id, etc.) which the listing page doesn't have.
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:draft,published',
                LeadValidationRules::statusCannotRevertFromPublished($lead),
            ],
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be either draft or published.',
        ]);

        // A lead in the assignment workflow is past Open already -
        // "publish" is a no-op for it, and must never knock it back to
        // Open (only the workflow changes such a status).
        $alreadyInWorkflow = $lead->isInWorkflow();

        if ($alreadyInWorkflow) {
            unset($validated['status']);
        }

        $wasPendingMultisite = $lead->isPendingMultisite();
        $wasDraft = $lead->isDraft();

        if ($wasPendingMultisite && ($validated['status'] ?? null) === 'published') {
            $this->assertPendingSitesReady($lead);
        }

        $lead = DB::transaction(function () use ($lead, $validated, $wasPendingMultisite) {

            $lead->update($validated);

            if ($wasPendingMultisite && $lead->status === 'published') {
                $lead = $this->expandMultisiteBatch($lead);
            }

            return $lead;
        });

        if ($wasDraft && $lead->status === 'published') {
            $this->workflow->leadPublished($lead, Auth::user());
            $lead = $lead->fresh();
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'status' => $lead->status,
            'status_label' => $lead->status_label,
            // The lead's own lead_id changes when it's expanded
            // (e.g. "1500" becomes "1500-1") - callers viewing this
            // lead by its old URL need this to redirect to the new one.
            'lead_id' => $lead->lead_id,
            // True when this request just turned one placeholder row
            // into N site leads - the table can't reflect that with
            // the usual single-row DOM update, so the frontend needs
            // to know to reload it from the server instead.
            'expanded' => $wasPendingMultisite && $lead->status === 'published',
            'message' => match (true) {
                $lead->status === 'draft' => "Lead #{$lead->display_id} saved as a draft.",
                $alreadyInWorkflow => "Lead #{$lead->display_id} is already with " . ($lead->assignee?->name ?? 'an Account Manager') . '.',
                $wasPendingMultisite => $this->batchPublishedMessage($lead),
                default => $this->publishedMessage($lead),
            },
            // Fresh Total/Draft/Published counts so the stat cards
            // at the top of the page can be updated without a
            // full page reload.
            'counts' => $this->scopedLeadCounts($user),
        ]);
    }

    /**
     * Turns a pending "Multiple Site" draft into its batch of site
     * leads - one per entry in its pending_sites (see
     * LeadCreationService::expand()).
     */
    private function expandMultisiteBatch(Lead $lead): Lead
    {
        return app(LeadCreationService::class)->expand($lead);
    }

    /**
     * A Multiple Site draft can only be published once it holds a
     * valid sites CSV - one MPAN per site. Checked before anything is
     * changed, so a refused publish leaves the draft exactly as it was.
     */
    private function assertPendingSitesReady(Lead $lead): void
    {
        if (!$lead->hasValidPendingSites()) {
            throw ValidationException::withMessages([
                'sites_csv' => LeadValidationRules::SITES_CSV_REQUIRED_MESSAGE
                    . ' Open the lead in Edit and upload the sites CSV.',
            ]);
        }

        $errors = MultisiteSitesCsv::validate(
            $lead->pending_sites,
            (int) $lead->sites_count,
            fn (int $i) => 'Site ' . ($i + 1),
            $lead->id
        );

        if ($errors) {
            throw ValidationException::withMessages(['sites_csv' => $errors]);
        }
    }

    /**
     * Header-only template for the Multiple Site sites CSV.
     */
    public function sitesTemplate()
    {
        return Excel::download(new MultisiteSitesTemplateExport(), 'multiple-site-template.csv', ExcelType::CSV);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $products = Product::orderBy('name')->get();
        } else {
            $assignedProductIds = $user->product_id ?? [];

            $products = Product::whereIn('id', $assignedProductIds)
                ->orderBy('name')
                ->get();
        }

        $formToken = $this->issueLeadFormToken();

        return view('leads.create', compact('products', 'formToken'));
    }

    /**
     * One token per visit to the create-lead form. store() consumes
     * it atomically before creating anything, so a double-click, a
     * slow-network retry, or resubmitting a stale back-button page
     * can't create a second batch of leads - see the migration
     * comment on lead_form_tokens for why this lives in the database
     * rather than the session (needs to be race-safe under real
     * concurrent requests, not just sequential ones).
     */
    private function issueLeadFormToken(): string
    {
        // Opportunistic cleanup of old, no-longer-relevant tokens -
        // this table only needs to hold tokens for forms that are
        // still open somewhere.
        DB::table('lead_form_tokens')
            ->where('created_at', '<', now()->subDay())
            ->delete();

        $token = (string) Str::uuid();

        DB::table('lead_form_tokens')->insert([
            'token' => $token,
            'created_at' => now(),
        ]);

        return $token;
    }

    private function leadFormTokenAlreadyUsed(?string $token): bool
    {
        return $token !== null && DB::table('lead_form_tokens')
            ->where('token', $token)
            ->whereNotNull('used_at')
            ->exists();
    }

    /**
     * Returns true the first time a given token is consumed, false
     * on every subsequent attempt (already used, or not a token this
     * app issued).
     */
    private function consumeLeadFormToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $consumed = DB::table('lead_form_tokens')
            ->where('token', $token)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        return $consumed > 0;
    }

    public function store(Request $request)
    {
        // A replay of a form that already created its lead(s) - its
        // MPANs are now "taken" by those very leads, so validating it
        // would show a misleading duplicate-MPAN error. Treat it as
        // the same no-op the token guard below does.
        if ($this->leadFormTokenAlreadyUsed($request->input('form_token'))) {
            return redirect()->route('leads.index');
        }

        $validated = $request->validate(
            LeadValidationRules::rules() + ['sites_csv' => self::sitesCsvFileRule()],
            LeadValidationRules::messages() + self::sitesCsvFileMessages()
        );

        unset($validated['sites_csv'], $validated['lead_date']);

        $sites = $this->sitesFromRequest($request, $validated, $validated['status'] === 'published');

        // Consumed only now that the submission is otherwise valid -
        // a validation failure doesn't burn the token, so the user
        // can fix the form and resubmit the same page. A second
        // request with this same (now-used) token - a double-click,
        // a slow-network retry, or resubmitting a stale back-button
        // page - is a no-op: nothing is created, and since the first
        // request already redirected with a success message, no
        // error is shown for this one either.
        if (!$this->consumeLeadFormToken($request->input('form_token'))) {
            return redirect()->route('leads.index');
        }

        $validated['created_by'] = Auth::id();

        $lead = app(LeadCreationService::class)->create($validated, $sites);

        $message = match (true) {
            $lead->base_lead_id !== null => $this->batchPublishedMessage($lead),
            $lead->isPendingMultisite() => "Lead #{$lead->display_id} saved as a draft. Its {$lead->sites_count} site leads will be created when it is published.",
            $lead->isDraft() => "Lead #{$lead->display_id} saved as a draft.",
            default => $this->publishedMessage($lead),
        };

        return redirect()
            ->route('leads.index')
            ->with('success', $message);
    }

    /**
     * "Lead #1500 published.", or "... published and assigned to
     * John Smith." when publishing handed it to its intended Account
     * Manager (imported leads).
     */
    private function publishedMessage(Lead $lead): string
    {
        $lead = $lead->fresh('assignee') ?? $lead;

        return $lead->isWithAccountManager() && $lead->assignee
            ? "Lead #{$lead->display_id} published and assigned to {$lead->assignee->name}."
            : "Lead #{$lead->display_id} published.";
    }

    /**
     * A Multiple Site lead just published into its batch - $site is
     * any one of its site leads.
     */
    private function batchPublishedMessage(Lead $site): string
    {
        $base = $site->base_lead_id;
        $count = $site->siblingSites()->count();
        $assignee = $site->fresh('assignee')?->assignee;

        return "Lead #{$base} published as {$count} site leads (#{$base}-1 to #{$base}-{$count})."
            . ($assignee ? " All sites assigned to {$assignee->name}." : '');
    }
    private static function sitesCsvFileRule(): array
    {
        return ['nullable', 'file', 'mimes:csv,txt', 'max:2048'];
    }

    private static function sitesCsvFileMessages(): array
    {
        return [
            'sites_csv.file' => 'Please choose a valid sites CSV file.',
            'sites_csv.mimes' => 'The sites file must be a CSV file.',
            'sites_csv.max' => 'The sites CSV may not be larger than 2MB.',
        ];
    }

    /**
     * Per-site data from the uploaded sites CSV for a Multiple Site
     * lead, validated against its site count (see MultisiteSitesCsv).
     * Required when $requireCsv (publishing); a draft may be saved
     * without one. Null for a Single Site lead.
     */
    private function sitesFromRequest(Request $request, array $validated, bool $requireCsv, ?int $exceptLeadId = null): ?array
    {
        if (($validated['number_of_sites'] ?? null) !== 'Multiple Site') {
            return null;
        }

        if (!$request->hasFile('sites_csv')) {
            if ($requireCsv) {
                throw ValidationException::withMessages([
                    'sites_csv' => LeadValidationRules::SITES_CSV_REQUIRED_MESSAGE,
                ]);
            }

            return null;
        }

        $sitesCount = (int) ($validated['sites_count'] ?? 0);

        if ($sitesCount < 1) {
            throw ValidationException::withMessages([
                'sites_count' => 'Please enter the number of sites (1-500) before uploading the sites CSV.',
            ]);
        }

        return MultisiteSitesCsv::fromUpload($request->file('sites_csv'), $sitesCount, $exceptLeadId);
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);

        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $products = Product::orderBy('name')->get();
        } else {
            // Now that a lead can be edited by someone other than its
            // creator (e.g. MIS User on a published lead), the editor's
            // own assigned products won't necessarily include the
            // lead's actual product - always add it so the form can
            // still show/preserve the existing selection.
            $assignedProductIds = $user->product_id ?? [];
            $assignedProductIds[] = $lead->product_id;

            $products = Product::whereIn('id', $assignedProductIds)
                ->orderBy('name')
                ->get();
        }

        return view('leads.edit', compact(
            'lead',
            'products'
        ));
    }
    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate(
            LeadValidationRules::rules($lead, requireSitesCountIfMultiple: false) + ['sites_csv' => self::sitesCsvFileRule()],
            LeadValidationRules::messages() + self::sitesCsvFileMessages()
        );

        unset($validated['sites_csv'], $validated['lead_date']);

        $validated['business_type'] = BusinessTypeMapper::map($validated['business_type'] ?? null);

        // The edit form's Update button always posts status=published;
        // for a lead already in the assignment workflow that must not
        // knock it back to Open - only the workflow changes its status.
        if ($lead->isInWorkflow()) {
            unset($validated['status']);
        }

        $wasDraft = $lead->isDraft();

        // Not yet expanded into its batch - a pending Multiple Site
        // draft, or a draft only now being switched to Multiple Site.
        $isUnexpanded = is_null($lead->base_lead_id) && ($wasDraft || $lead->isPendingMultisite());
        $willBeMultisite = (array_key_exists('number_of_sites', $validated)
            ? $validated['number_of_sites']
            : $lead->number_of_sites) === 'Multiple Site';
        $willExpand = $isUnexpanded && $willBeMultisite && ($validated['status'] ?? $lead->status) === 'published';

        if ($isUnexpanded && $willBeMultisite) {
            // The per-site MPANs replace the single MPAN field.
            $validated['mpan'] = null;

            // A freshly uploaded sites CSV replaces what was held.
            if ($request->hasFile('sites_csv')) {
                $validated['pending_sites'] = $this->sitesFromRequest(
                    $request,
                    [
                        'number_of_sites' => 'Multiple Site',
                        'sites_count' => $validated['sites_count'] ?? $lead->sites_count,
                    ],
                    requireCsv: false,
                    exceptLeadId: $lead->id
                );
            }
        } elseif ($isUnexpanded && !$willBeMultisite) {
            // No longer Multiple Site - drop any held site data.
            $validated['pending_sites'] = null;
        }

        if ($willExpand) {
            $this->assertPendingSitesReady(
                (clone $lead)->forceFill(array_intersect_key($validated, array_flip(['sites_count', 'pending_sites'])))
            );
        }

        $lead = DB::transaction(function () use ($lead, $validated, $willExpand) {

            $lead->update($validated);

            if ($willExpand) {
                $lead = $this->expandMultisiteBatch($lead);
            }

            return $lead;
        });

        if ($wasDraft && $lead->status === 'published') {
            $this->workflow->leadPublished($lead, Auth::user());
        }

        $message = match (true) {
            $willExpand && $lead->status === 'published' => $this->batchPublishedMessage($lead),
            $wasDraft && $lead->status === 'published' => $this->publishedMessage($lead),
            default => "Lead #{$lead->display_id} updated.",
        };

        return redirect()
            ->route('leads.index')
            ->with('success', $message);
    }
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load('product', 'creator', 'assignee');

        // Only needed for AU Savers leads, but cheap enough (and
        // small enough) to just always load rather than branching -
        // the Pricing card itself is what's gated by product.
        $lead->load('currentPricing.supplier');
        $suppliers = Supplier::orderBy('name')->get();

        // The Assign card (pick / change the Account Manager) is for
        // Admin / Super Admin / MIS only. Only fetched for them -
        // $accountManagers is the same list assign() validates
        // against, so the dropdown can never offer an Account Manager
        // the server would then reject.
        $user = Auth::user();
        $canSeeAssignment = $user->isAdminOrAbove() || $user->isMis();
        $canAssign = $user->can('assign', $lead);
        $accountManagers = $canSeeAssignment ? $this->workflow->assignableAccountManagers($lead) : collect();

        // The Assigned Team card (MIS / Account Manager / current
        // owner + workflow buttons + history) is for anyone who can
        // assign, plus the Account Manager on the lead.
        $canSeeWorkflow = $canSeeAssignment || $lead->isTeamMember($user);
        $canViewPricing = $user->can('viewPricing', $lead);
        $canReviewPricing = $user->can('reviewPricing', $lead);
        $canUpdateStatus = $user->can('updateWorkflowStatus', $lead);

        $assignmentHistory = collect();

        if ($canSeeWorkflow) {
            $lead->load('assigner.role', 'accountExecutive', 'accountManager', 'closer.role', 'holder.role', 'lostBy.role');
            $lead->assignee?->loadMissing('role');
            $assignmentHistory = $lead->assignments;

            // e.g. an Account Manager the lead was reassigned away
            // from: no pricing decisions (or their reasons).
            if (!$canViewPricing) {
                $assignmentHistory = $assignmentHistory
                    ->reject(fn ($entry) => in_array($entry->action, LeadAssignment::PRICING_ACTIONS, true))
                    ->values();
            }
        }

        if ($canViewPricing && $lead->currentPricing) {
            $lead->currentPricing->loadMissing('reviewer.role');
        }

        LeadLogger::leadViewed($lead);

        return view(
            'leads.show',
            compact(
                'lead', 'suppliers',
                'canSeeAssignment', 'canAssign', 'accountManagers',
                'canSeeWorkflow', 'canViewPricing', 'canReviewPricing', 'canUpdateStatus',
                'assignmentHistory'
            )
        );
    }

    /**
     * Assign a published lead to an Account Manager (or reassign it
     * to another one). Sets assigned_to and moves the lead to With
     * Account Manager, then notifies the Account Manager (dashboard
     * bell + email). See LeadWorkflowService for the rules.
     */
    public function assign(Request $request, Lead $lead)
    {
        $this->authorize('assign', $lead);

        $validated = $request->validate([
            'account_manager_id' => ['required', 'integer'],
        ], [
            'account_manager_id.required' => 'Please select an Account Manager.',
            'account_manager_id.integer' => 'Please select a valid Account Manager.',
        ]);

        $assigner = Auth::user();
        $reassigning = $lead->account_manager_id !== null || $lead->assigned_to !== null;

        [$lead, $am, $changed] = $this->workflow->assignToAccountManager($lead, (int) $validated['account_manager_id'], $assigner);

        return response()->json([
            'success' => true,
            'changed' => $changed,
            'message' => match (true) {
                !$changed => "Lead #{$lead->display_id} is already assigned to {$am->name}.",
                $reassigning => "Lead #{$lead->display_id} reassigned to {$am->name}.",
                default => "Lead #{$lead->display_id} assigned to {$am->name}.",
            },
            'status' => $lead->status,
            'status_label' => $lead->status_label,
            'assigned_to' => [
                'id' => $am->id,
                'name' => $am->name,
                'email' => $am->email,
            ],
            'counts' => $this->scopedLeadCounts($assigner),
        ]);
    }

    /**
     * Account Manager: approve the lead's current pricing.
     */
    public function approvePricing(Request $request, Lead $lead)
    {
        $this->authorize('reviewPricing', $lead);

        $validated = $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        $lead = $this->workflow->reviewPricing($lead, Auth::user(), true, $validated['note'] ?? null);

        return $this->workflowResponse($lead, "Pricing approved for Lead #{$lead->display_id}.");
    }

    /**
     * Account Manager: decline the lead's current pricing (reason
     * required) - the lead goes back to MIS for re-pricing.
     */
    public function declinePricing(Request $request, Lead $lead)
    {
        $this->authorize('reviewPricing', $lead);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], [
            'note.required' => 'Please enter the reason you are declining this pricing.',
        ]);

        $lead = $this->workflow->reviewPricing($lead, Auth::user(), false, $validated['note']);

        $assigner = $lead->assigner;

        return $this->workflowResponse($lead, $assigner && !$assigner->trashed() && $assigner->id !== Auth::id()
            ? "Pricing declined. {$assigner->name} has been asked to publish updated pricing."
            : 'Pricing declined.');
    }

    /**
     * Account Manager: Hold / Lost / Close, from the one
     * "Update Lead Status" control.
     */
    public function setAccountManagerStatus(Request $request, Lead $lead)
    {
        $this->authorize('updateWorkflowStatus', $lead);

        $validated = $request->validate([
            'status' => ['required', Rule::in([Lead::STATUS_HOLD, Lead::STATUS_LOST, Lead::STATUS_CLOSED])],
            'note' => ['nullable', 'string', 'max:1000', 'required_if:status,' . Lead::STATUS_LOST],
        ], [
            'status.required' => 'Please choose Hold, Lost or Close.',
            'status.in' => 'Please choose Hold, Lost or Close.',
            'note.required_if' => 'Please enter the reason this lead was lost.',
        ]);

        $lead = $this->workflow->setAccountManagerStatus($lead, Auth::user(), $validated['status'], $validated['note'] ?? null);

        return $this->workflowResponse($lead, match ($lead->status) {
            Lead::STATUS_HOLD => "Lead #{$lead->display_id} put on hold.",
            Lead::STATUS_LOST => "Lead #{$lead->display_id} marked as lost.",
            default => "Lead #{$lead->display_id} closed.",
        });
    }

    private function workflowResponse(Lead $lead, string $message)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $lead->status,
            'status_label' => $lead->status_label,
            'assigned_to' => $lead->assigned_to,
        ]);
    }

    // Redesigned Lead Show page (UI/UX preview). Same data as show(),
    // rendered by a separate view - leads.show / show() are untouched.
    // public function show2(Lead $lead)
    // {

    //     $lead->load('product', 'creator');

    //     LeadLogger::leadViewed($lead);

    //     return view(
    //         'leads.show2',
    //         compact('lead')
    //     );
    // }
    /**
     * A draft can only be deleted by its creator, a published lead
     * only by Admin / Super Admin - see LeadPolicy::delete().
     */
    public function destroy(Request $request, Lead $lead)
    {
        if (Auth::user()->cannot('delete', $lead)) {
            abort(403, 'You are not allowed to delete this lead.');
        }

        $lead->delete();

        $message = "Lead #{$lead->display_id} deleted.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('leads.index')
            ->with('success', $message);
    }
    public function storeReminder(Request $request, Lead $lead)
    {
        $this->authorize('view', $lead);

        $validated = $request->validate([
            'reminder_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'reminder_time' => [
                'required',
                'date_format:H:i',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'reminder_date.required' => 'Please select a reminder date.',
            'reminder_date.date' => 'Please enter a valid reminder date.',
            'reminder_date.after_or_equal' => 'Reminder date cannot be in the past.',
            'reminder_time.required' => 'Please select a reminder time.',
            'reminder_time.date_format' => 'Please enter a valid reminder time.',
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['created_by'] = Auth::id();

        $reminder = LeadReminder::create($validated);

        LeadLogger::reminderCreated($lead, $reminder);

        // The show2 page submits this via fetch() so it can show a
        // toast instead of a full page reload; a plain form POST
        // (no JS) still falls back to the classic redirect below.
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'reminder' => $reminder->load('creator:id,name'),
                'message' => 'Reminder added successfully.',
            ]);
        }

        return redirect()
            ->route('leads.show', $lead)
            ->with('success', 'Reminder added successfully.');
    }
    public function reminders(Lead $lead)
    {
        $this->authorize('view', $lead);

        $reminders = $lead->reminders()
            ->with('creator')
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->get();

        return response()->json($reminders);
    }
    public function logs(Lead $lead)
    {
        $this->authorize('view', $lead);

        $logs = $lead->logs()->with('user:id,name');

        // Pricing entries name the supplier and carry the rates /
        // values and decline reasons - only for those who can see
        // the lead's pricing.
        if (Auth::user()->cannot('viewPricing', $lead)) {
            $logs->where(function ($q) {
                $q->whereNull('module')->orWhere('module', '!=', 'pricing');
            });
        }

        $logs = $logs->get();

        return response()->json($logs);
    }

    public function destroyReminder(LeadReminder $reminder)
    {
        if (!$this->canManageReminder($reminder)) {
            abort(403, 'You are not allowed to delete this reminder.');
        }

        LeadLogger::reminderDeleted($reminder);

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder deleted successfully.',
        ]);
    }

    /**
     * Update a reminder's date/time/note. Owner, or Admin / Super
     * Admin - same rules as storeReminder() for the fields
     * themselves, same ownership convention as the rest of this
     * controller (see destroy(), edit()) for who may act.
     */
    public function updateReminder(Request $request, LeadReminder $reminder)
    {
        if (!$this->canManageReminder($reminder)) {
            abort(403, 'You are not allowed to edit this reminder.');
        }

        $validated = $request->validate([
            'reminder_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'reminder_time' => [
                'required',
                'date_format:H:i',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'reminder_date.required' => 'Please select a reminder date.',
            'reminder_date.date' => 'Please enter a valid reminder date.',
            'reminder_date.after_or_equal' => 'Reminder date cannot be in the past.',
            'reminder_time.required' => 'Please select a reminder time.',
            'reminder_time.date_format' => 'Please enter a valid reminder time.',
        ]);

        $reminder->update($validated);

        return response()->json([
            'success' => true,
            'reminder' => $reminder->fresh()->load('creator:id,name'),
            'message' => 'Reminder updated successfully.',
        ]);
    }

    /**
     * Owner of the reminder, or Admin / Super Admin - and only while
     * they can still see the lead it belongs to.
     */
    private function canManageReminder(LeadReminder $reminder): bool
    {
        if (!$reminder->lead || Auth::user()->cannot('view', $reminder->lead)) {
            return false;
        }

        if ((int) $reminder->created_by === Auth::id()) {
            return true;
        }

        return Auth::user()->isAdminOrAbove();
    }
}