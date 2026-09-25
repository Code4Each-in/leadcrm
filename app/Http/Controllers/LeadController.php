<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesProducts;
use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\LeadCreationService;
use App\Services\LeadIdGenerator;
use App\Services\LeadLogger;
use App\Services\LeadWorkflowService;
use App\Support\BusinessTypeMapper;
use App\Support\LeadValidationRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
     * every Open or Assigned lead (on top of leads they created
     * themselves); everyone else sees only leads they created or
     * that are (or were) assigned to them as AE / Account Manager.
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

        return $query->where(function (Builder $q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('assigned_to', $user->id)
                ->orWhere('account_executive_id', $user->id)
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
        // "Assigned" covers every live workflow stage (Assigned, In
        // Progress, With Account Manager, Sent Back) - closed leads
        // are done, so they drop out of it.
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
        if ($lead->isInWorkflow()) {
            unset($validated['status']);
        }

        $wasPendingMultisite = $lead->isPendingMultisite();

        $lead->update($validated);

        if ($wasPendingMultisite && $lead->status === 'published') {
            $lead = $this->expandMultisiteBatch($lead);
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
                $lead->status === 'draft' => 'Lead moved to draft.',
                $wasPendingMultisite => "Multiple Site lead published successfully ({$lead->sites_count} sites).",
                $lead->isInWorkflow() => 'Lead is already assigned.',
                default => 'Lead published - it is now Open.',
            },
            // Fresh Total/Draft/Published counts so the stat cards
            // at the top of the page can be updated without a
            // full page reload.
            'counts' => $this->scopedLeadCounts($user),
        ]);
    }

    /**
     * Turns a "Multiple Site" lead that was saved as a draft (a
     * single placeholder row, base_lead_id still null) into its full
     * batch of site leads, now that it's being published. The
     * placeholder itself becomes site #1 - not deleted and
     * recreated - so any notes, documents, reminders or audit log
     * entries already attached to it survive. Sites 2..N are new
     * rows cloned from it.
     */
    private function expandMultisiteBatch(Lead $lead): Lead
    {
        return DB::transaction(function () use ($lead) {

            $baseId = $lead->lead_id;
            $sitesCount = (int) $lead->sites_count;

            $lead->update([
                'lead_id' => "{$baseId}-1",
                'base_lead_id' => $baseId,
                'site_sequence' => 1,
            ]);

            $attributes = Arr::except(
                Arr::only($lead->getAttributes(), $lead->getFillable()),
                ['lead_id', 'base_lead_id', 'site_sequence']
            );

            for ($sequence = 2; $sequence <= $sitesCount; $sequence++) {

                Lead::create(array_merge($attributes, [
                    'lead_id' => "{$baseId}-{$sequence}",
                    'base_lead_id' => $baseId,
                    'site_sequence' => $sequence,
                ]));
            }

            return $lead->fresh();
        });
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
        $validated = $request->validate(
            LeadValidationRules::rules(),
            LeadValidationRules::messages()
        );

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

        $sitesCount = (int) ($validated['sites_count'] ?? 0);
        $isMultisitePublish = ($validated['number_of_sites'] ?? null) === 'Multiple Site'
            && $validated['status'] === 'published';

        $lead = app(LeadCreationService::class)->create($validated);

        if ($isMultisitePublish) {
            return redirect()
                ->route('leads.index')
                ->with(
                    'success',
                    "Multiple Site lead created successfully ({$sitesCount} sites)."
                );
        }

        return redirect()
            ->route('leads.index')
            ->with(
                'success',
                $lead->status === 'draft'
                    ? 'Lead saved as draft successfully.'
                    : 'Lead published successfully.'
            );
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
            LeadValidationRules::rules($lead, requireSitesCountIfMultiple: false),
            LeadValidationRules::messages()
        );

        $validated['business_type'] = BusinessTypeMapper::map($validated['business_type'] ?? null);

        // The edit form's Update button always posts status=published;
        // for a lead already in the assignment workflow that must not
        // knock it back to Open - only the workflow changes its status.
        if ($lead->isInWorkflow()) {
            unset($validated['status']);
        }

        $wasPendingMultisite = $lead->isPendingMultisite();

        $lead->update($validated);

        if ($wasPendingMultisite && $lead->status === 'published') {
            $lead = $this->expandMultisiteBatch($lead);
        }

        return redirect()
            ->route('leads.index')
            ->with(
                'success',
                $wasPendingMultisite && $lead->status === 'published'
                    ? "Multiple Site lead published successfully ({$lead->sites_count} sites)."
                    : 'Lead updated successfully.'
            );
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

        // The Assigned card (pick / change the AE) is for Admin /
        // Super Admin / MIS only. Only fetched for them - $aeUsers is
        // the same list assign() validates against, so the dropdown
        // can never offer an AE the server would then reject.
        $user = Auth::user();
        $canSeeAssignment = $user->isAdminOrAbove() || $user->isMis();
        $canAssign = $user->can('assign', $lead);
        $aeUsers = $canSeeAssignment ? $this->workflow->assignableAes($lead) : collect();

        // The Assigned Team card (MIS / AE / Account Manager / current
        // owner + workflow buttons + history) is for anyone who can
        // assign, plus the AE / Account Manager on the lead.
        $canSeeWorkflow = $canSeeAssignment || $lead->isTeamMember($user);
        $canStartProcess = $user->can('startProcess', $lead);
        $canMoveToAm = $user->can('moveToAccountManager', $lead);
        $canSendBack = $user->can('sendBack', $lead);
        $canUpdateStatus = $user->can('updateWorkflowStatus', $lead);

        $accountManagers = $canMoveToAm ? $this->workflow->assignableAccountManagers() : collect();

        $assignmentHistory = collect();

        if ($canSeeWorkflow) {
            $lead->load('assigner.role', 'accountExecutive', 'accountManager', 'processStarter', 'closer.role', 'holder.role', 'lostBy.role');
            $lead->assignee?->loadMissing('role');
            $assignmentHistory = $lead->assignments;
        }

        LeadLogger::leadViewed($lead);

        return view(
            'leads.show',
            compact(
                'lead', 'suppliers',
                'canSeeAssignment', 'canAssign', 'aeUsers',
                'canSeeWorkflow', 'canStartProcess', 'canMoveToAm', 'canSendBack', 'canUpdateStatus',
                'accountManagers', 'assignmentHistory'
            )
        );
    }

    /**
     * Assign an Open lead to an Account Executive (or reassign one
     * already in the workflow). Sets assigned_to and moves the lead
     * to Assigned, then notifies the AE (dashboard bell + email).
     * See LeadWorkflowService for the rules.
     */
    public function assign(Request $request, Lead $lead)
    {
        $this->authorize('assign', $lead);

        $validated = $request->validate([
            'ae_id' => ['required', 'integer'],
        ], [
            'ae_id.required' => 'Please select an Account Executive.',
            'ae_id.integer' => 'Please select a valid Account Executive.',
        ]);

        $assigner = Auth::user();

        [$lead, $ae, $changed] = $this->workflow->assignToAe($lead, (int) $validated['ae_id'], $assigner);

        return response()->json([
            'success' => true,
            'changed' => $changed,
            'message' => $changed
                ? "Lead assigned to {$ae->name}."
                : "Lead is already assigned to {$ae->name}.",
            'status' => $lead->status,
            'status_label' => $lead->status_label,
            'assigned_to' => [
                'id' => $ae->id,
                'name' => $ae->name,
                'email' => $ae->email,
            ],
            'counts' => $this->scopedLeadCounts($assigner),
        ]);
    }

    /**
     * AE: Assigned -> In Progress.
     */
    public function startProcess(Lead $lead)
    {
        $this->authorize('startProcess', $lead);

        $lead = $this->workflow->startProcess($lead, Auth::user());

        return $this->workflowResponse($lead, 'Process started - the lead is now In Progress.');
    }

    /**
     * AE: hand the lead to a chosen Account Manager.
     */
    public function moveToAccountManager(Request $request, Lead $lead)
    {
        $this->authorize('moveToAccountManager', $lead);

        $validated = $request->validate([
            'account_manager_id' => ['required', 'integer'],
        ], [
            'account_manager_id.required' => 'Please select an Account Manager.',
            'account_manager_id.integer' => 'Please select a valid Account Manager.',
        ]);

        [$lead, $am] = $this->workflow->moveToAccountManager($lead, (int) $validated['account_manager_id'], Auth::user());

        return $this->workflowResponse($lead, "Lead moved to Account Manager {$am->name}.");
    }

    /**
     * Account Manager: return the lead to the AE.
     */
    public function sendBack(Request $request, Lead $lead)
    {
        $this->authorize('sendBack', $lead);

        $validated = $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        [$lead, $ae] = $this->workflow->sendBackToAe($lead, Auth::user(), $validated['note'] ?? null);

        return $this->workflowResponse($lead, "Lead sent back to {$ae->name}.");
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
            Lead::STATUS_HOLD => 'Lead put on hold.',
            Lead::STATUS_LOST => 'Lead marked as lost.',
            default => 'Lead closed.',
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
    public function destroy(Lead $lead)
    {
        $user = Auth::user();

        // Admin and Super Admin can delete any lead
        if ($user->isAdminOrAbove()) {
            $lead->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully.',
            ]);
        }

        // Normal users:
        // They can only delete their own Draft leads
        if (
            $lead->created_by !== $user->id ||
            $lead->status !== 'draft'
        ) {
            abort(403, 'You are not allowed to delete this lead.');
        }

        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
    public function storeReminder(Request $request, Lead $lead)
    {
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
        $reminders = $lead->reminders()
            ->with('creator')
            ->orderBy('reminder_date')
            ->orderBy('reminder_time')
            ->get();

        return response()->json($reminders);
    }
    public function logs(Lead $lead)
    {
        $logs = $lead->logs()
            ->with('user:id,name')
            ->get();

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
     * Owner of the reminder, or Admin / Super Admin. Same convention
     * used everywhere else in this controller (destroy(), edit()).
     */
    private function canManageReminder(LeadReminder $reminder): bool
    {
        if ($reminder->created_by === Auth::id()) {
            return true;
        }

        return Auth::user()->isAdminOrAbove();
    }
}