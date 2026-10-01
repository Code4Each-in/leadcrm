<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\LeadLogger;

class LeadObserver
{
    /**
     * Lead Staging: an AU Savers lead being published - from Draft, or
     * created / imported / expanded straight into Open - starts its
     * journey at Pricing Request Received instead of Open. Only on
     * the change to published, so an Open lead from before Lead
     * Staging is left as it is by a later edit.
     */
    public function saving(Lead $lead): void
    {
        if ($lead->status === Lead::STATUS_PUBLISHED
            && (!$lead->exists || $lead->isDirty('status'))
            && $lead->requiresPricing()) {
            $lead->status = Lead::STATUS_PRICING_REQUEST_RECEIVED;
        }
    }

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
            // The raw per-site JSON is noise in an audit entry - just
            // record how many sites the uploaded CSV held.
            ->map(fn ($change, $field) => $field === 'pending_sites'
                ? array_map(fn ($sites) => self::sitesSummary($sites), $change)
                : $change)
            ->all();

        LeadLogger::leadUpdated($lead, $changes);
    }

    private static function sitesSummary(mixed $sites): ?string
    {
        if (is_string($sites)) {
            $sites = json_decode($sites, true);
        }

        return is_array($sites) ? count($sites) . ' site(s) from CSV' : null;
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
