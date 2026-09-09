<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Product;
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


            $recordsTotal = (clone $query)->count();

            $recordsFiltered = $query->count();

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

        return view('leads.index');
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
            ->route('leads.create')
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

        return view(
            'leads.show',
            compact('lead')
        );
    }
    public function destroy(Lead $lead)
    {
        $lead->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully.',
            ]);
        }

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

        LeadReminder::create($validated);

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
    public function destroyReminder(LeadReminder $reminder)
    {
        if ($reminder->created_by !== Auth::id()) {
            abort(403, 'You are not allowed to delete this reminder.');
        }

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder deleted successfully.',
        ]);
    }
}
