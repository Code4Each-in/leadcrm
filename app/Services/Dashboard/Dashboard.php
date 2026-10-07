<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * One role's dashboard: the partial it renders and the data that
 * partial needs. DashboardController picks the implementation from
 * the user's role (see DashboardController::DASHBOARDS), so giving a
 * role its own dashboard later is one new subclass and one map entry.
 *
 * Every lead query a subclass runs must stay inside what the user may
 * see (Lead::scopeVisibleTo() / LeadPolicy::view()) - a dashboard
 * must never count or link to a lead the user couldn't open.
 */
abstract class Dashboard
{
    /**
     * The dashboard.partials.* view for this role.
     */
    abstract public function view(): string;

    /**
     * Variables for view().
     */
    abstract public function data(User $user): array;

    /**
     * The user's own reminders for today and the next 7 days, on leads
     * they can still see. Shown on every role's dashboard.
     */
    public function upcomingReminders(User $user, int $limit = 5): Collection
    {
        $today = now()->toDateString();

        return $this->reminders($user)
            ->whereBetween('reminder_date', [$today, now()->addDays(7)->toDateString()])
            ->take($limit)
            ->get();
    }

    /**
     * The user's reminders for today only - the dashboard's reminder
     * popup. Earlier and later days never pop up.
     */
    public function todaysReminders(User $user, int $limit = 20): Collection
    {
        return $this->reminders($user)
            ->whereDate('reminder_date', now()->toDateString())
            ->take($limit)
            ->get();
    }

    /**
     * The user's own reminders on leads they can still see, soonest
     * first.
     */
    private function reminders(User $user): Builder
    {
        return LeadReminder::query()
            ->with('lead')
            ->where('created_by', $user->id)
            ->whereHas('lead', fn (Builder $q) => $q->visibleTo($user))
            ->orderBy('reminder_date')
            ->orderBy('reminder_time');
    }

    /**
     * Lead count per stored status for $query.
     *
     * @return array<string, int>
     */
    protected function statusCounts(Builder $query): array
    {
        return (clone $query)
            ->toBase()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /**
     * The plain-array shape the dashboard tables render (and that can
     * be cached).
     */
    protected function leadRow(Lead $lead, ?string $note = null, ?CarbonInterface $since = null): array
    {
        return [
            'display_id' => $lead->display_id,
            'company' => $lead->company_business_name ?: ($lead->customer_name ?: '-'),
            'status' => $lead->status,
            'status_label' => $lead->status_label,
            'note' => $note,
            'since' => ($since ?? $lead->created_at)?->toIso8601String(),
            'url' => route('leads.show', $lead),
        ];
    }

    protected function sqlTime(CarbonInterface $time): string
    {
        return $time->format('Y-m-d H:i:s');
    }
}
