<?php

namespace App\Observers;

use App\Models\LeadPricing;
use App\Services\LeadLogger;

class LeadPricingObserver
{
    public function created(LeadPricing $pricing): void
    {
        LeadLogger::pricingCreated($pricing->lead, $pricing);
    }

    public function updated(LeadPricing $pricing): void
    {
        $changes = collect($pricing->getChanges())
            ->except(['updated_at'])
            ->mapWithKeys(fn ($new, $field) => [
                $field => [
                    'old' => $pricing->getOriginal($field),
                    'new' => $new,
                ],
            ])
            ->all();

        LeadLogger::pricingUpdated($pricing, $changes);
    }

    public function deleted(LeadPricing $pricing): void
    {
        LeadLogger::pricingDeleted($pricing);
    }
}
