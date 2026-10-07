<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\User;

/**
 * Account Executive: the leads they created (all an AE can see - see
 * Lead::scopeVisibleTo()) - what needs their action (leads sent back
 * to them, drafts to publish) and how many are moving or closed.
 */
class AccountExecutiveDashboard extends Dashboard
{
    private const LIST_SIZE = 5;

    public function view(): string
    {
        return 'dashboard.partials.account-executive';
    }

    public function data(User $user): array
    {
        $counts = $this->statusCounts(Lead::visibleTo($user));

        $notInProgress = array_sum(array_intersect_key($counts, array_flip([
            Lead::STATUS_DRAFT,
            Lead::STATUS_SENT_BACK,
            Lead::STATUS_CLOSED,
            Lead::STATUS_LOST,
        ])));

        return [
            'kpis' => [
                'sentBack' => $counts[Lead::STATUS_SENT_BACK] ?? 0,
                'drafts' => $counts[Lead::STATUS_DRAFT] ?? 0,
                // Published and moving: not a draft, sent back or finished.
                'inProgress' => array_sum($counts) - $notInProgress,
                'closed' => $counts[Lead::STATUS_CLOSED] ?? 0,
            ],
            'sentBack' => Lead::visibleTo($user)
                ->where('status', Lead::STATUS_SENT_BACK)
                ->oldest('updated_at')
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead, null, $lead->updated_at))
                ->all(),
            'drafts' => Lead::visibleTo($user)
                ->where('status', Lead::STATUS_DRAFT)
                ->oldest()
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead))
                ->all(),
        ];
    }
}
