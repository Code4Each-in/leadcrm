<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Admin / Super Admin: the headline lead figures, what needs attention,
 * and the latest leads. Detailed reporting belongs on the Reporting
 * page, not here.
 *
 * Both roles see every published lead (Lead::scopeVisibleTo()), so the
 * figures are the same for everyone at this level and are cached for
 * CACHE_SECONDS rather than recomputed on every page load. Drafts are
 * private to their creator (LeadPolicy::view()) and are left out.
 */
class AdminDashboard extends Dashboard
{
    public const CACHE_KEY = 'dashboard:admin:v2';

    public const CACHE_SECONDS = 60;

    /** "Needs attention" thresholds. */
    private const UNASSIGNED_HOURS = 24;

    private const HOLD_DAYS = 7;

    private const LIST_SIZE = 6;

    public function view(): string
    {
        return 'dashboard.partials.admin';
    }

    public function data(User $user): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, fn () => [
            'generatedAt' => now()->toIso8601String(),
            'kpis' => $this->kpis(),
            'attention' => $this->attention(),
            'latest' => $this->leads()
                ->with('product:id,name')
                ->latest()
                ->take(self::LIST_SIZE)
                ->get()
                ->map(fn (Lead $lead) => $this->leadRow($lead))
                ->all(),
        ]);
    }

    /**
     * Every published lead.
     */
    private function leads(): Builder
    {
        return Lead::query()->where('status', '!=', Lead::STATUS_DRAFT);
    }

    private function kpis(): array
    {
        $closedThisMonth = $this->leads()
            ->where('status', Lead::STATUS_CLOSED)
            ->where('closed_at', '>=', $this->sqlTime(now()->startOfMonth()))
            ->count();

        return [
            'total' => $this->leads()->count(),
            'open' => $this->leads()->openUnassigned()->count(),
            // Live and with someone (Hold included - it is still theirs).
            'inProgress' => $this->leads()->assignedActive()->count(),
            'closedThisMonth' => $closedThisMonth,
        ];
    }

    /**
     * Leads waiting on someone - only the items that currently have
     * any, so an empty list means nothing is stuck.
     */
    private function attention(): array
    {
        $items = [
            [
                'label' => 'Unassigned for over ' . self::UNASSIGNED_HOURS . ' hours',
                'owner' => 'MIS / Admin to assign',
                'icon' => 'mdi-account-alert',
                'tone' => 'danger',
                'count' => $this->leads()->openUnassigned()
                    ->where('created_at', '<', $this->sqlTime(now()->subHours(self::UNASSIGNED_HOURS)))
                    ->count(),
            ],
            [
                'label' => 'Pricing awaiting approval',
                'owner' => 'Account Manager to review',
                'icon' => 'mdi-file-clock',
                'tone' => 'warning',
                'count' => Lead::awaitingPricingApproval()->count(),
            ],
            [
                'label' => 'Pricing declined',
                'owner' => 'MIS to re-price',
                'icon' => 'mdi-file-undo',
                'tone' => 'danger',
                'count' => Lead::pricingDeclined()->count(),
            ],
            [
                'label' => 'Sent back to the AE',
                'owner' => 'Account Executive to update',
                'icon' => 'mdi-undo-variant',
                'tone' => 'warning',
                'count' => $this->leads()->where('status', Lead::STATUS_SENT_BACK)->count(),
            ],
            [
                'label' => 'On Hold for over ' . self::HOLD_DAYS . ' days',
                'owner' => 'Account Manager to follow up',
                'icon' => 'mdi-pause-circle',
                'tone' => 'info',
                'count' => $this->leads()->where('status', Lead::STATUS_HOLD)
                    ->where('hold_at', '<', $this->sqlTime(now()->subDays(self::HOLD_DAYS)))
                    ->count(),
            ],
        ];

        return array_values(array_filter($items, fn (array $item) => $item['count'] > 0));
    }
}
