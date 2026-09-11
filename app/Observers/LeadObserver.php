<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\LeadLogger;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        LeadLogger::leadCreated($lead);
    }

    /**
     * Builds an old/new diff from the change Eloquent just tracked for
     * this save, excluding timestamp noise, and hands it to the
     * logger. Covers every path that calls $lead->update()/save() -
     * the full edit form and the inline status toggle alike - so
     * neither controller needs its own explicit "log this update"
     * call (and can't accidentally double-log the same change).
     */
    public function updated(Lead $lead): void
    {
        $changes = collect($lead->getChanges())
            ->except(['updated_at'])
            ->mapWithKeys(fn ($new, $field) => [
                $field => [
                    'old' => $lead->getOriginal($field),
                    'new' => $new,
                ],
            ])
            ->all();

        LeadLogger::leadUpdated($lead, $changes);
    }

    public function deleted(Lead $lead): void
    {
        LeadLogger::leadDeleted($lead);
    }

    public function restored(Lead $lead): void
    {
        LeadLogger::leadRestored($lead);
    }
}
