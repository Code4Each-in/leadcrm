<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Account Manager: the leads they currently hold (leads.assigned_to)
 * and what is waiting on them - pricing to approve and new
 * assignments.
 */
class AccountManagerDashboard extends Dashboard
{
    private const LIST_SIZE = 5;

    private const NEW_ASSIGNMENT_HOURS = 48;

    public function view(): string
    {
        return 'dashboard.partials.account-manager';
    }

    public function data(User $user): array
    {
        $mine = fn (): Builder => Lead::visibleTo($user)->where('assigned_to', $user->id);

        $newlyAssigned = fn (): Builder => $mine()
            ->where('am_assigned_at', '>=', $this->sqlTime(now()->subHours(self::NEW_ASSIGNMENT_HOURS)))
            ->whereNotIn('status', Lead::FINISHED_STATUSES);

        return [
            'kpis' => [
                'awaitingApproval' => $mine()->awaitingPricingApproval()->count(),
                'newlyAssigned' => $newlyAssigned()->count(),
                'active' => $mine()->whereIn('status', Lead::ACCOUNT_MANAGER_ACTIVE_STATUSES)->count(),
                'hold' => $mine()->where('status', Lead::STATUS_HOLD)->count(),
            ],
            'awaitingApproval' => $mine()
                ->awaitingPricingApproval()
                ->oldest('am_assigned_at')
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead, null, $lead->am_assigned_at))
                ->all(),
            'newlyAssigned' => $newlyAssigned()
                ->latest('am_assigned_at')
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead, null, $lead->am_assigned_at))
                ->all(),
        ];
    }
}
