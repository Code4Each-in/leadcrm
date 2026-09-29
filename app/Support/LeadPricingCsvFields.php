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
     * What each column accepts, shown in the pricing import page's
     * Expected Columns table.
     */
    public static function hints(): array
    {
        return [
            'lead_id' => 'Lead ID of an existing AU Savers lead, e.g. 1500 or 1500-1.',
            'supplier' => 'Supplier name, e.g. Octopus Energy.',
            'rate_type' => 'single or multi.',
            'contract_term_months' => 'Whole number of months, 1-120.',
            'total_eac_kwh' => 'A number. Required for single rate; calculated from the three consumption columns for multi rate.',
            'day_consumption_kwh' => 'A number. Required for multi rate.',
            'evening_consumption_kwh' => 'A number. Required for multi rate.',
            'night_consumption_kwh' => 'A number. Required for multi rate.',
            'sc_pence_per_day' => 'Standing charge, pence per day.',
            'unit_rate_pence' => 'Pence per kWh - the single rate, or the day rate for multi rate.',
            'night_unit_rate_pence' => 'Pence per kWh. Required for multi rate.',
            'evening_unit_rate_pence' => 'Pence per kWh. Required for multi rate.',
            'uplift_pence' => 'Pence per kWh.',
            'status' => 'draft or published. Leave blank to import as a draft.',
        ];
    }
}
