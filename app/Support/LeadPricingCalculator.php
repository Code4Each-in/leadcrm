<?php

namespace App\Support;

/**
 * All Pricing math in one place, so store/update and any future
 * display recompute identically. Rate/charge fields are entered and
 * stored in pence - conversion to pounds happens only here, by
 * dividing by 100 (never by string manipulation), so e.g. 110p/kWh
 * correctly becomes £1.10/kWh rather than £110/kWh.
 *
 * Uplift is a flat p/kWh addition (not a percentage) applied to each
 * applicable base rate individually: Customer Rate = Base Rate +
 * Uplift. Annual Spend is calculated from the customer (uplifted)
 * rates, since that's what the customer is actually charged.
 */
class LeadPricingCalculator
{
    /**
     * Total EAC (kWh/year): the sum of the three consumption periods
     * for a multi-rate contract, or the entered figure directly for
     * a single-rate contract.
     */
    public static function totalEac(array $data): float
    {
        if (($data['rate_type'] ?? null) === 'multi') {
            return (float) ($data['day_consumption_kwh'] ?? 0)
                + (float) ($data['evening_consumption_kwh'] ?? 0)
                + (float) ($data['night_consumption_kwh'] ?? 0);
        }

        return (float) ($data['total_eac_kwh'] ?? 0);
    }

    /**
     * Customer Rate (p/kWh) = Supplier/Base Rate + Uplift.
     */
    public static function customerRate(float $baseRatePence, float $upliftPence): float
    {
        return $baseRatePence + $upliftPence;
    }

    public static function penceToPounds(float $pence): float
    {
        return $pence / 100;
    }

    /**
     * Annual Standing Charge (£) = SC p/day x 365, converted to pounds.
     */
    public static function annualStandingCharge(float $scPencePerDay): float
    {
        return self::penceToPounds($scPencePerDay * 365);
    }

    /**
     * Annual Spend (£), for either contract type:
     *
     * Multi-rate:
     *   [(Day kWh x Day Customer Rate) + (Evening kWh x Evening Customer Rate)
     *     + (Night kWh x Night Customer Rate)] / 100 + Annual Standing Charge
     *
     * Single-rate:
     *   Total EAC x (Unit Customer Rate / 100) + Annual Standing Charge
     */
    public static function annualSpend(array $data): float
    {
        $uplift = (float) ($data['uplift_pence'] ?? 0);
        $standingCharge = self::annualStandingCharge((float) ($data['sc_pence_per_day'] ?? 0));

        if (($data['rate_type'] ?? null) === 'multi') {
            $dayCost = (float) ($data['day_consumption_kwh'] ?? 0)
                * self::customerRate((float) ($data['unit_rate_pence'] ?? 0), $uplift);

            $eveningCost = (float) ($data['evening_consumption_kwh'] ?? 0)
                * self::customerRate((float) ($data['evening_unit_rate_pence'] ?? 0), $uplift);

            $nightCost = (float) ($data['night_consumption_kwh'] ?? 0)
                * self::customerRate((float) ($data['night_unit_rate_pence'] ?? 0), $uplift);

            return self::penceToPounds($dayCost + $eveningCost + $nightCost) + $standingCharge;
        }

        $customerRate = self::customerRate((float) ($data['unit_rate_pence'] ?? 0), $uplift);

        return (float) ($data['total_eac_kwh'] ?? 0) * self::penceToPounds($customerRate) + $standingCharge;
    }
}
