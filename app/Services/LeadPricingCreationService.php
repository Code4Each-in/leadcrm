<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadPricing;
use App\Support\LeadPricingCalculator;

/**
 * Creates a LeadPricing record from an already-validated attribute
 * array - the calculate-then-create steps shared by
 * LeadPricingController::store() (one record, from the manual Add
 * Pricing form) and LeadPricingCsvController::import() (many records,
 * one per CSV row), so both create pricing the exact same way.
 */
class LeadPricingCreationService
{
    /**
     * @param array $validated Must already be validated against
     *   LeadPricingValidationRules::rules(), and include 'created_by'.
     *   'lead_id' may already be set (CSV import resolves it per row
     *   before calling this) or supplied via $lead.
     */
    public function create(array $validated, ?Lead $lead = null): LeadPricing
    {
        if ($lead) {
            $validated['lead_id'] = $lead->id;
        }

        $validated['total_eac_kwh'] = LeadPricingCalculator::totalEac($validated);
        $validated['annual_spend'] = LeadPricingCalculator::annualSpend($validated);

        return LeadPricing::create($validated);
    }
}
