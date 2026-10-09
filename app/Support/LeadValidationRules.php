<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Validation\Rule;

/**
 * Single source of truth for Lead field validation - previously
 * duplicated between LeadController::store() and ::update(). Also
 * used by the CSV importer (LeadCsvController) so an imported row can
 * never pass validation that a manually-submitted row would fail.
 */
class LeadValidationRules
{
    public const MPAN_TAKEN_MESSAGE =
        'This MPAN is already used by another lead.';

    public const SITES_COUNT_MISMATCH_MESSAGE =
        'The number of MPANs must match the number of sites';

    public const DUPLICATE_MPAN_MESSAGE =
        'Each site must have a different MPAN.';

    public const SITES_CSV_REQUIRED_MESSAGE =
        'Please upload a sites CSV with one MPAN per site for a Multiple Site lead.';

    /**
     * @param Lead|null $lead The lead being updated, so the
     *   published-can't-revert-to-draft guard can be applied. Null
     *   for a brand new lead (manual create or CSV import).
     * @param bool $requireSitesCountIfMultiple store()/CSV import
     *   require sites_count when number_of_sites is "Multiple Site";
     *   update() does not (an already-published batch's site count
     *   is fixed).
     */
    public static function rules(?Lead $lead = null, bool $requireSitesCountIfMultiple = true, bool $allowTakenMpan = false): array
    {
        return [

            // Product is the ONLY required field
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'status' => array_values(array_filter([
                'required',
                'in:draft,published',
                $lead ? self::statusCannotRevertFromPublished($lead) : null,
            ])),

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

            'number_of_sites' => [
                'nullable',
                'in:Single Site,Multiple Site',
            ],

            // Only meaningful (and required) when number_of_sites is
            // "Multiple Site" - the count of individual site leads to
            // create, each getting its own "{base}-{n}" Lead ID.
            'sites_count' => array_values(array_filter([
                'nullable',
                $requireSitesCountIfMultiple ? 'required_if:number_of_sites,Multiple Site' : null,
                'integer',
                'min:1',
                'max:500',
            ])),

            // Exactly 13 digits - digits:13 already implies numeric-only.
            // Must not already be used by another lead - see
            // MpanRegistry for the scope, and for why an edit that
            // leaves the MPAN unchanged is never blocked - unless the
            // user confirmed the duplicate ($allowTakenMpan).
            'mpan' => [
                'nullable',
                'digits:13',
                MpanRegistry::rule($lead, $allowTakenMpan),
            ],

            // 6-8 digits - digits_between:x,y already implies numeric-only.
            'mprn' => [
                'nullable',
                'digits_between:6,8',
            ],

            // 8-10 digits - digits_between:x,y already implies numeric-only.
            'spid' => [
                'nullable',
                'digits_between:8,10',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            // Only supplied by the CSV import ("Lead Date" column) -
            // kept separate from created_at.
            'lead_date' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],

        ];
    }

    public static function messages(): array
    {
        return [

            'product_id.required' =>
                'Please select a product.',

            'product_id.exists' =>
                'Please select a valid product.',

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

            'funds_term_months.in' =>
                'Please select a valid funding term.',

            'home_owner.in' =>
                'Please select Yes or No for Home Owner.',

            'vat_registered.in' =>
                'Please select Yes or No for VAT Registered.',

            'number_of_sites.in' =>
                'Please select a valid number of sites.',

            'sites_count.required_if' =>
                'Please enter the number of sites (1-500).',

            'sites_count.integer' =>
                'Number of sites must be a whole number.',

            'sites_count.min' =>
                'Number of sites must be at least 1.',

            'sites_count.max' =>
                'Number of sites cannot be more than 500.',

            'mpan.digits' =>
                'Please enter a valid MPAN. It must contain exactly 13 digits.',

            'mprn.digits_between' =>
                'Please enter a valid MPRN. It must contain between 6 and 8 digits.',

            'spid.digits_between' =>
                'Please enter a valid SPID. It must contain between 8 and 10 digits.',

            'lead_date.date_format' =>
                'Lead Date must be a valid date in YYYY-MM-DD format.',

            'lead_date.before_or_equal' =>
                'Lead Date cannot be in the future.',
        ];
    }

    /**
     * messages() reworded for the Lead CSV import, where a value is
     * typed into a cell rather than picked from a list - "must be
     * one of ..." instead of "Please select ...".
     */
    public static function importMessages(): array
    {
        return array_merge(self::messages(), [

            'status.in' =>
                'Status must be draft or published.',

            'company_type.in' =>
                'Company Type must be Limited, Sole Trader, Partnership or Limited Liability Partnership.',

            'business_start_date.date' =>
                'Business Start Date must be a valid date in YYYY-MM-DD format.',

            'date_of_birth.date' =>
                'Date of Birth must be a valid date in YYYY-MM-DD format.',

            'funds_term_months.in' =>
                'Funding term must be 12, 24, 36, 48, 60 or 72.',

            'home_owner.in' =>
                'Home Owner must be Yes or No.',

            'vat_registered.in' =>
                'VAT Registered must be Yes or No.',

            'loan_purpose.in' =>
                'Loan Purpose must be one of the values listed under Expected Columns.',

            'number_of_sites.in' =>
                'Number of Sites must be Single Site or Multiple Site.',

            'sites_count.required_if' =>
                'sites_count is required for a Multiple Site row (1-500).',

            'mpan.digits' =>
                'MPAN must contain exactly 13 digits.',

            'mprn.digits_between' =>
                'MPRN must contain 6 to 8 digits.',

            'spid.digits_between' =>
                'SPID must contain 8 to 10 digits.',
        ]);
    }

    /**
     * Publishing is one-way: once a lead is published, nobody can
     * move it back to draft. Moved from LeadController so the CSV
     * importer (which never updates an existing lead) doesn't need
     * to know about it, while store()/update() still get it via the
     * $lead parameter.
     */
    public static function statusCannotRevertFromPublished(Lead $lead): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($lead) {
            // Open (published) and Assigned leads alike.
            if ($lead->isPublishedOrBeyond() && $value === 'draft') {
                $fail('A published lead cannot be changed back to draft.');
            }
        };
    }
}
