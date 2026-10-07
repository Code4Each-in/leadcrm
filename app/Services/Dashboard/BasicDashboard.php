<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\User;

/**
 * Roles with no workflow of their own yet (QA User, and any role not
 * mapped in DashboardController::DASHBOARDS): the leads they created.
 * Notifications and reminders are added for every role by the
 * dashboard page itself.
 */
class BasicDashboard extends Dashboard
{
    public function view(): string
    {
        return 'dashboard.partials.basic';
    }

    public function data(User $user): array
    {
        $mine = fn () => Lead::visibleTo($user)->where('created_by', $user->id);

        $counts = $this->statusCounts($mine());
        $drafts = $counts[Lead::STATUS_DRAFT] ?? 0;

        return [
            'kpis' => [
                'total' => array_sum($counts),
                'published' => array_sum($counts) - $drafts,
                'drafts' => $drafts,
            ],
            'recent' => $mine()
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead))
                ->all(),
        ];
    }
}
