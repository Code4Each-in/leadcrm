<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\LeadLogger;

class LeadObserver
{
    /**
     * Lead Staging: an AU Savers lead being published - from Draft, or
     * created / imported / expanded straight into Open - starts its
     * journey at Lead Submitted to Pricing instead of Open; MIS moves
     * it on from there. Only on the change to published, so an Open
     * lead from before Lead Staging is left as it is by a later edit.
     *
     * A draft stage (Call Back / Awaiting Additional Information) only
     * belongs on an AU Savers draft - publishing, or switching to
     * another product, drops it.
     */
    public function saving(Lead $lead): void
    {
        if ($lead->status === Lead::STATUS_PUBLISHED
            && (!$lead->exists || $lead->isDirty('status'))
            && $lead->requiresPricing()) {
            $lead->status = Lead::STATUS_LEAD_SUBMITTED_TO_PRICING;
        }

        if ($lead->draft_stage !== null && (!$lead->isDraft() || !$lead->requiresPricing())) {
            $lead->draft_stage = null;
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
