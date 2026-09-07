<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Application::with('product');

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

            $applications = $query
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                'draw' => intval($request->draw),

                'recordsTotal' => $recordsTotal,

                'recordsFiltered' => $recordsFiltered,

                'data' => $applications,
            ]);
        }

        return view('applications.index');
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

        return view('applications.create', compact('products'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([

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
            ],

            /*
            * Client requirement:
            * Phone No. - 10 digits
            */
            'phone_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            /*
            * Client requirement:
            * Mobile No. - 10 digits
            */
            'mobile_no' => [
                'nullable',
                'regex:/^[0-9]{10}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],


            'gross_sales' => [
                'required_if:product_id,1,2',
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_required' => [
                'required_if:product_id,1,2',
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_term_months' => [
                'required_if:product_id,1,2',
                'nullable',
                'in:12,24,36,48,60,72',
            ],

            'home_owner' => [
                'required_if:product_id,1,2',
                'nullable',
                'in:Yes,No',
            ],

            'vat_registered' => [
                'required_if:product_id,1,2',
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

            'supply_address' => [
                'required_if:product_id,3',
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
                'required_if:product_id,3',
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'mpan' => [
                'required_if:product_id,3',
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:13',
            ],

            'mprn' => [
                'required_if:product_id,3',
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:6',
            ],

            'spid' => [
                'required_if:product_id,3',
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

            'gross_sales.required_if' =>
                'Gross Sales field is required.',

            'funds_required.required_if' =>
                'Funds Required field is required.',

            'funds_term_months.required_if' =>
                'Term of Funds Required field is required.',

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.required_if' =>
                'Home Owner field is required.',

            'vat_registered.required_if' =>
                'VAT Registered field is required.',

            'supply_address.required_if' =>
                'Supply Address field is required.',

            'number_of_sites.required_if' =>
                'Number of Sites field is required.',

            'mpan.required_if' =>
                'MPAN field is required.',

            'mpan.regex' =>
                'MPAN must contain numbers only.',

            'mpan.min_digits' =>
                'MPAN must contain at least 13 digits.',

            'mprn.required_if' =>
                'MPRN field is required.',

            'mprn.regex' =>
                'MPRN must contain numbers only.',

            'mprn.min_digits' =>
                'MPRN must contain at least 6 digits.',

            'spid.required_if' =>
                'SPID field is required.',

            'spid.regex' =>
                'SPID must contain numbers only.',

            'spid.min_digits' =>
                'SPID must contain at least 8 digits.',
        ]);
        $validated['created_by'] = Auth::id();
        $application = Application::create($validated);

        return redirect()
            ->route('applications.create')
            ->with(
                'success',
                $application->status === 'draft'
                    ? 'Application saved as draft successfully.'
                    : 'Application published successfully.'
            );
    }
    public function edit(Application $application)
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

        return view('applications.edit', compact(
            'application',
            'products'
        ));
    }
    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([

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

            /*
            * Product 1 and 2
            */
            'gross_sales' => [
                'required_if:product_id,1,2',
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_required' => [
                'required_if:product_id,1,2',
                'nullable',
                'numeric',
                'min:0',
            ],

            'funds_term_months' => [
                'required_if:product_id,1,2',
                'nullable',
                'in:12,24,36,48,60,72',
            ],

            'home_owner' => [
                'required_if:product_id,1,2',
                'nullable',
                'in:Yes,No',
            ],

            'vat_registered' => [
                'required_if:product_id,1,2',
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

            /*
            * Product 3
            */
            'supply_address' => [
                'required_if:product_id,3',
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
                'required_if:product_id,3',
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'mpan' => [
                'required_if:product_id,3',
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:13',
            ],

            'mprn' => [
                'required_if:product_id,3',
                'nullable',
                'regex:/^[0-9]+$/',
                'min_digits:6',
            ],

            'spid' => [
                'required_if:product_id,3',
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

            'gross_sales.required_if' =>
                'Gross Sales is required for this product.',

            'funds_required.required_if' =>
                'Funds Required is required for this product.',

            'funds_term_months.required_if' =>
                'Please select the term of funds required.',

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.required_if' =>
                'Please specify if the client is a home owner.',

            'vat_registered.required_if' =>
                'Please specify if the client is VAT registered.',

            'supply_address.required_if' =>
                'Supply Address is required for this product.',

            'number_of_sites.required_if' =>
                'Please select the number of sites.',

            'mpan.required_if' =>
                'MPAN is required for this product.',

            'mprn.required_if' =>
                'MPRN is required for this product.',

            'spid.required_if' =>
                'SPID is required for this product.',

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

        $application->update($validated);

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application updated successfully.');
    }
    public function show(Application $application)
    {
         $application->load('product', 'creator');

        return view(
            'applications.show',
            compact('application')
        );
    }
    public function destroy(Application $application)
    {
        $application->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully.',
            ]);
        }

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
