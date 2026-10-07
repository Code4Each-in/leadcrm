<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * MIS User: turning published leads into priced, assigned ones - the
 * assignment queue and the pricing queue (AU Savers). MIS sees every
 * published lead (Lead::scopeVisibleTo()).
 */
class MisDashboard extends Dashboard
{
    private const LIST_SIZE = 5;

    public function view(): string
    {
        return 'dashboard.partials.mis';
    }

    public function data(User $user): array
    {
        $awaitingPricing = fn (): Builder => Lead::visibleTo($user)->awaitingPricing();
        $pricingDeclined = fn (): Builder => Lead::visibleTo($user)->pricingDeclined();

        return [
            'kpis' => [
                'awaitingAssignment' => Lead::visibleTo($user)->openUnassigned()->count(),
                'pricingToDo' => $awaitingPricing()->count() + $pricingDeclined()->count(),
                'pendingAutoAssign' => Lead::visibleTo($user)
                    ->where('status', '!=', Lead::STATUS_DRAFT)
                    ->whereNotNull('intended_account_manager_id')
                    ->count(),
                'myDrafts' => Lead::where('created_by', $user->id)->where('status', Lead::STATUS_DRAFT)->count(),
            ],
            'unassigned' => Lead::visibleTo($user)
                ->openUnassigned()
                ->oldest()
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead))
                ->all(),
            'pricingQueue' => $this->pricingQueue($pricingDeclined(), $awaitingPricing()),
        ];
    }

    /**
     * Declined pricing first (the Account Manager is waiting on it),
     * then leads with no published pricing yet - oldest first in each.
     * Each row's note is its pricing stage.
     */
    private function pricingQueue(Builder $declined, Builder $awaiting): array
    {
        $declinedRows = $declined
            ->oldest()
            ->take(self::LIST_SIZE)
            ->get()
            ->map(fn (Lead $lead) => ['note_class' => 'pill-issues'] + $this->leadRow($lead, 'Declined'));

        $awaitingRows = $awaiting
            ->with('currentPricing')
            ->oldest()
            ->take(max(0, self::LIST_SIZE - $declinedRows->count()))
            ->get()
            ->map(fn (Lead $lead) => $this->leadRow($lead, $lead->currentPricing ? 'Pricing in draft' : 'No pricing yet'));

        return $declinedRows->concat($awaitingRows)->all();
    }
}
