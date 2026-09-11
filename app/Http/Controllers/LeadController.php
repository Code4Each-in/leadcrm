<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Product;
use App\Services\LeadLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Lead::with('product');

            $user = Auth::user();
            $roleName = strtolower($user->role->name);

            if (!in_array($roleName, ['super admin', 'admin'])) {
                $query->where('created_by', $user->id);
            }

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
        $roleName = strtolower($user->role->name);

        $scopedLeads = Lead::query();

        if (!in_array($roleName, ['super admin', 'admin'])) {
            $scopedLeads->where('created_by', $user->id);
        }

        $totalLeadsCount = (clone $scopedLeads)->count();
        $draftLeadsCount = (clone $scopedLeads)->where('status', 'draft')->count();
        $publishedLeadsCount = (clone $scopedLeads)->where('status', 'published')->count();

        $productLeadCounts = (clone $scopedLeads)
            ->selectRaw('product_id, count(*) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        $products = $this->scopedProductsQuery($user, $roleName)
            ->get()
            ->map(function ($product) use ($productLeadCounts) {
                $product->leads_count = $productLeadCounts[$product->id] ?? 0;
                return $product;
            });

        return view('leads.index', compact(
            'products',
            'totalLeadsCount',
            'draftLeadsCount',
            'publishedLeadsCount'
        ));
    }

    /**
     * Products visible to the current user.
     *
     * Super Admin / Admin see every product (they can see every
     * lead too). Normal users only see the products assigned to
     * them (user->product_id), matching the same scoping already
     * used in create() - and now also used for the "Leads by
     * Product" stat cards, so a normal user isn't shown counts for
     * products they don't even have access to create leads for.
     */
    private function scopedProductsQuery($user, string $roleName)
    {
        if (in_array($roleName, ['super admin', 'admin'])) {
            return Product::orderBy('name');
        }

        $assignedProductIds = $user->product_id ?? [];

        return Product::whereIn('id', $assignedProductIds)->orderBy('name');
    }

    /**
     * Total / Draft / Published counts, scoped the same way the
     * leads table itself is scoped for the current user. Shared by
     * index() (initial page load) and updateStatus() (so the stat
     * cards can be refreshed in place after an inline status change,
     * without a full page reload).
     */
    private function scopedLeadCounts($user, string $roleName): array
    {
        $scopedLeads = Lead::query();

        if (!in_array($roleName, ['super admin', 'admin'])) {
            $scopedLeads->where('created_by', $user->id);
        }

        return [
            'total' => (clone $scopedLeads)->count(),
            'draft' => (clone $scopedLeads)->where('status', 'draft')->count(),
            'published' => (clone $scopedLeads)->where('status', 'published')->count(),
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
        $validated = $request->validate([
            'status' => [
                'required',
                'in:draft,published',
            ],
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be either draft or published.',
        ]);

        $lead->update($validated);

        $user = Auth::user();
        $roleName = strtolower($user->role->name);

        return response()->json([
            'success' => true,
            'status' => $lead->status,
            'message' => $lead->status === 'draft'
                ? 'Lead moved to draft.'
                : 'Lead published.',
            // Fresh Total/Draft/Published counts so the stat cards
            // at the top of the page can be updated without a
            // full page reload.
            'counts' => $this->scopedLeadCounts($user, $roleName),
        ]);
    }
    public function create()
    {
        $user = Auth::user();

        $roleName = strtolower($user->role->name);

        if ($roleName === 'super admin') {
            $products = Product::orderBy('name')->get();
        } else {
            $assignedProductIds = $user->product_id ?? [];

            $products = Product::whereIn('id', $assignedProductIds)
                ->orderBy('name')
                ->get();
        }

        return view('leads.create', compact('products'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([

            // Product is the ONLY required field
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'company_type' => [
                'nullable',
                'string',
                'max:255',
                'in:Limited,Sole Trader,Partnership,Limited Liability Partnership',
            ],

            'company_business_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'business_start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'business_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'business_registered_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'business_trading_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'same_as_registered_address' => [
                'nullable',
                'boolean',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'phone_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'mobile_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            // NFS / AF4U fields - optional but validated if entered
            'gross_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_required' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_term_months' => [
                'nullable',
                'in:12,24,36,48,60,72',
            ],

            'home_owner' => [
                'nullable',
                'in:Yes,No',
            ],

            'vat_registered' => [
                'nullable',
                'in:Yes,No',
            ],

            'loan_purpose' => [
                'nullable',
                'string',
                Rule::in([
                    'Fund vehicle, equipment or machinery',
                    'Expansion / growth',
                    'Refinancing a loan',
                    'Tax payment',
                    'Working capital',
                    'Other',
                ]),
            ],

            'funds_usage_details' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // AU Savers fields - optional but validated if entered
            'supply_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'postcode' => [
                'nullable',
                'regex:/^[A-Za-z0-9 ]+$/',
                'max:10',
            ],

            'number_of_sites' => [
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'mpan' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:13',
            ],

            'mprn' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:6',
            ],

            'spid' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:8',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ], [

            'product_id.required' =>
                'Please select a product.',

            'company_type.in' =>
                'Please select a valid company type.',

            'business_start_date.before_or_equal' =>
                'Business start date cannot be in the future.',

            'date_of_birth.before_or_equal' =>
                'Date of birth cannot be in the future.',

            'email.email' =>
                'Please enter a valid email address.',

            'phone_no.regex' =>
                'Phone number must contain exactly 10 digits.',

            'mobile_no.regex' =>
                'Mobile number must contain exactly 10 digits.',

            'postcode.regex' =>
                'Postcode can contain only letters, numbers and spaces.',

            'postcode.max' =>
                'Postcode cannot be longer than 10 characters.',

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.in' =>
                'Please select Yes or No for Home Owner.',

            'vat_registered.in' =>
                'Please select Yes or No for VAT Registered.',

            'number_of_sites.in' =>
                'Please select a valid number of sites.',

            'mpan.regex' =>
                'MPAN must contain numbers only.',

            'mpan.min_digits' =>
                'MPAN must contain at least 13 digits.',

            'mprn.regex' =>
                'MPRN must contain numbers only.',

            'mprn.min_digits' =>
                'MPRN must contain at least 6 digits.',

            'spid.regex' =>
                'SPID must contain numbers only.',

            'spid.min_digits' =>
                'SPID must contain at least 8 digits.',
        ]);

        $validated['created_by'] = Auth::id();

        $lead = Lead::create($validated);

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
        $user = Auth::user();

        $roleName = strtolower($user->role->name);

        // Admins can edit any lead
        if (!in_array($roleName, ['admin', 'super admin'])) {

            // Normal user can only edit their own leads
            if ($lead->created_by !== $user->id) {
                abort(403, 'You are not allowed to edit this lead.');
            }
        }

        if ($roleName === 'super admin') {
            $products = Product::orderBy('name')->get();
        } else {
            $assignedProductIds = $user->product_id ?? [];

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
        $user = Auth::user();
        $roleName = strtolower($user->role->name);

        // Admins can update any lead
        if (!in_array($roleName, ['admin', 'super admin'])) {

            // Normal users can only update their own leads
            if ($lead->created_by !== $user->id) {
                abort(403, 'You are not allowed to update this lead.');
            }
        }
        
        $validated = $request->validate([

            // Product is the ONLY required field
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'company_type' => [
                'nullable',
                'string',
                'max:255',
                'in:Limited,Sole Trader,Partnership,Limited Liability Partnership',
            ],

            'company_business_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'company_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'business_start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'business_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'business_registered_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'business_trading_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'same_as_registered_address' => [
                'nullable',
                'boolean',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'phone_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'mobile_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            // NFS / AF4U fields - optional but validated if entered
            'gross_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_required' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_term_months' => [
                'nullable',
                'in:12,24,36,48,60,72',
            ],

            'home_owner' => [
                'nullable',
                'in:Yes,No',
            ],

            'vat_registered' => [
                'nullable',
                'in:Yes,No',
            ],

            'loan_purpose' => [
                'nullable',
                'string',
                Rule::in([
                    'Fund vehicle, equipment or machinery',
                    'Expansion / growth',
                    'Refinancing a loan',
                    'Tax payment',
                    'Working capital',
                    'Other',
                ]),
            ],

            'funds_usage_details' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // AU Savers fields - optional but validated if entered
            'supply_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'postcode' => [
                'nullable',
                'regex:/^[A-Za-z0-9 ]+$/',
                'max:10',
            ],

            'number_of_sites' => [
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'mpan' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:13',
            ],

            'mprn' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:6',
            ],

            'spid' => [
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:8',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ], [

            'product_id.required' =>
                'Please select a product.',

            'company_type.in' =>
                'Please select a valid company type.',

            'business_start_date.before_or_equal' =>
                'Business start date cannot be in the future.',

            'date_of_birth.before_or_equal' =>
                'Date of birth cannot be in the future.',

            'email.email' =>
                'Please enter a valid email address.',

            'phone_no.regex' =>
                'Phone number must contain exactly 10 digits.',

            'mobile_no.regex' =>
                'Mobile number must contain exactly 10 digits.',

            'postcode.regex' =>
                'Postcode can contain only letters, numbers and spaces.',

            'postcode.max' =>
                'Postcode cannot be longer than 10 characters.',

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.in' =>
                'Please select Yes or No for Home Owner.',

            'vat_registered.in' =>
                'Please select Yes or No for VAT Registered.',

            'number_of_sites.in' =>
                'Please select a valid number of sites.',

            'mpan.regex' =>
                'MPAN must contain numbers only.',

            'mpan.min_digits' =>
                'MPAN must contain at least 13 digits.',

            'mprn.regex' =>
                'MPRN must contain numbers only.',

            'mprn.min_digits' =>
                'MPRN must contain at least 6 digits.',

            'spid.regex' =>
                'SPID must contain numbers only.',

            'spid.min_digits' =>
                'SPID must contain at least 8 digits.',
        ]);

        $lead->update($validated);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }
    public function show(Lead $lead)
    {

        $lead->load('product', 'creator');

        LeadLogger::leadViewed($lead);

        return view(
            'leads.show',
            compact('lead')
        );
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
        $roleName = strtolower($user->role->name);

        // Admin and Super Admin can delete any lead
        if (in_array($roleName, ['admin', 'super admin'])) {
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

        $roleName = strtolower(Auth::user()->role->name ?? '');

        return in_array($roleName, ['admin', 'super admin']);
    }
}