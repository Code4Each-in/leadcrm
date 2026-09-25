<?php

namespace App\Support;

use App\Models\LeadPricing;

/**
 * Single source of truth for Pricing field validation, mirroring
 * LeadValidationRules's shape (a plain static-method class - this
 * app has no FormRequest classes).
 */
class LeadPricingValidationRules
{
    /**
     * @param LeadPricing|null $pricing The pricing record being
     *   updated, so the published-can't-revert-to-draft guard can be
     *   applied. Null when creating a new record.
     */
    public static function rules(?LeadPricing $pricing = null): array
    {
        return [

            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'rate_type' => [
                'required',
                'in:single,multi',
            ],

            'contract_term_months' => [
                'required',
                'integer',
                'min:1',
                'max:120',
            ],

            'sc_pence_per_day' => [
                'required',
                'numeric',
                'min:0',
            ],

            'uplift_pence' => [
                'required',
                'numeric',
                'min:0',
            ],

            // Single-rate: the one unit rate. Multi-rate: the Day rate.
            'unit_rate_pence' => [
                'required',
                'numeric',
                'min:0',
            ],

            'night_unit_rate_pence' => [
                'required_if:rate_type,multi',
                'nullable',
                'numeric',
                'min:0',
            ],

            'evening_unit_rate_pence' => [
                'required_if:rate_type,multi',
                'nullable',
                'numeric',
                'min:0',
            ],

            // Single-rate: entered directly. Multi-rate: recomputed
            // server-side from the three consumption fields below, so
            // this is only actually required for single-rate.
            'total_eac_kwh' => [
                'required_if:rate_type,single',
                'nullable',
                'numeric',
                'min:0',
            ],

            'day_consumption_kwh' => [
                'required_if:rate_type,multi',
                'nullable',
                'numeric',
                'min:0',
            ],

            'evening_consumption_kwh' => [
                'required_if:rate_type,multi',
                'nullable',
                'numeric',
                'min:0',
            ],

            'night_consumption_kwh' => [
                'required_if:rate_type,multi',
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => array_values(array_filter([
                'required',
                'in:draft,published',
                $pricing ? self::statusCannotRevertFromPublished($pricing) : null,
            ])),

        ];
    }

    public static function messages(): array
    {
        return [

            'supplier_id.required' =>
                'Please select a supplier.',

            'supplier_id.exists' =>
                'Please select a valid supplier.',

            'rate_type.required' =>
                'Please select single-rate or multi-rate.',

            'contract_term_months.required' =>
                'Please enter the contract term (in months).',

            'contract_term_months.integer' =>
                'Contract term must be a whole number of months.',

            'night_unit_rate_pence.required_if' =>
                'Night Unit Charge is required for a multi-rate contract.',

            'evening_unit_rate_pence.required_if' =>
                'Evening Unit Charge is required for a multi-rate contract.',

            'total_eac_kwh.required_if' =>
                'Please enter the total EAC for a single-rate contract.',

            'day_consumption_kwh.required_if' =>
                'Day consumption is required for a multi-rate contract.',

            'evening_consumption_kwh.required_if' =>
                'Evening consumption is required for a multi-rate contract.',

            'night_consumption_kwh.required_if' =>
                'Night consumption is required for a multi-rate contract.',

        ];
    }

    /**
     * Publishing is one-way, same rule as LeadValidationRules -
     * once a pricing record is published, it can't be moved back to
     * draft (any further change becomes a new record instead).
     */
    public static function statusCannotRevertFromPublished(LeadPricing $pricing): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($pricing) {
            if ($pricing->status === 'published' && $value === 'draft') {
                $fail('Published pricing cannot be changed back to draft.');
            }
        };
    }
}
