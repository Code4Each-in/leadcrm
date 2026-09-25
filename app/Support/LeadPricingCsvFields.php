<?php

namespace App\Support;

/**
 * The CSV column set for the Pricing template/import - every field
 * the manual Add Pricing form collects (see
 * LeadPricingValidationRules::rules()), plus "lead_id" (the
 * business-facing id, e.g. "1500" or "1500-1" for a multisite site -
 * not the internal numeric primary key) so each row can target a
 * different Lead, and "supplier" (the supplier's name, not its
 * numeric id - LeadPricingCsvController resolves it to supplier_id
 * before validating, the same way LeadCsvController resolves the
 * selected product into product_id).
 */
class LeadPricingCsvFields
{
    /**
     * @return array<int,string> ordered field keys
     */
    public static function fields(): array
    {
        return [
            'lead_id',
            'supplier',
            'rate_type',
            'contract_term_months',
            'total_eac_kwh',
            'day_consumption_kwh',
            'evening_consumption_kwh',
            'night_consumption_kwh',
            'sc_pence_per_day',
            'unit_rate_pence',
            'night_unit_rate_pence',
            'evening_unit_rate_pence',
            'uplift_pence',
            'status',
        ];
    }

    /**
     * One-line, human-readable note per field explaining allowed
     * values/format, used as a hint row under the template header and
     * in the import page's field reference.
     */
    public static function hints(): array
    {
        return [
            'lead_id' => 'the Lead ID this pricing belongs to (e.g. 1500 or 1500-1) - must be an existing AU Savers lead',
            'supplier' => 'a supplier name, e.g. Octopus Energy',
            'rate_type' => 'single or multi',
            'contract_term_months' => 'whole number, 1-120',
            'total_eac_kwh' => 'number - required for single-rate, ignored for multi-rate (calculated from the three consumption columns instead)',
            'day_consumption_kwh' => 'number - required for multi-rate',
            'evening_consumption_kwh' => 'number - required for multi-rate',
            'night_consumption_kwh' => 'number - required for multi-rate',
            'sc_pence_per_day' => 'number, pence per day',
            'unit_rate_pence' => 'number, pence/kWh - the single rate, or the Day rate for multi-rate',
            'night_unit_rate_pence' => 'number, pence/kWh - required for multi-rate',
            'evening_unit_rate_pence' => 'number, pence/kWh - required for multi-rate',
            'uplift_pence' => 'number, pence/kWh',
            'status' => 'draft or published - leave blank to import as draft',
        ];
    }
}
