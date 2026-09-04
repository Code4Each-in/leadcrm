<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
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

            'company_type' => [
                'nullable',
                'string',
                'max:255',
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
            ],

            'business_trading_address' => [
                'nullable',
                'string',
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
                'string',
                'max:30',
            ],

            'mobile_no' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

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
                'integer',
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
                'in:Fund vehicle, equipment or machinery,Expansion / growth,Refinancing a loan,Tax payment,Working capital,Other',
            ],

            'funds_usage_details' => [
                'nullable',
                'string',
                'max:2000',
            ],


            'supply_address' => [
                'nullable',
                'string',
            ],

            'postcode' => [
                'nullable',
                'string',
                'max:20',
            ],

            'number_of_sites' => [
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            'mpan' => [
                'nullable',
                'string',
                'max:50',
            ],

            'mprn' => [
                'nullable',
                'string',
                'max:50',
            ],

            'spid' => [
                'nullable',
                'string',
                'max:50',
            ],


            'status' => [
                'required',
                'in:draft,published',
            ],


            'notes' => [
                'nullable',
                'string',
            ],
        ]);

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
}
