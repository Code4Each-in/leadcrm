<?php

namespace App\Support;

use App\Models\LeadStage;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * The Lead Staging stages (App\Models\LeadStage), loaded once per
 * request - registered as a scoped singleton (AppServiceProvider) and
 * flushed whenever a stage is saved or deleted.
 *
 * "All" includes deleted stages: a lead's history (and a lead still
 * at a stage retired before Stage Management) keeps its name and
 * colour. "Active" is what can be picked.
 */
class LeadStages
{
    private ?Collection $all = null;

    public function flush(): void
    {
        $this->all = null;
    }

    /**
     * Every stage, deleted ones included, with its group: group by group
     * (the groups' Order), then by each stage's Order within its group -
     * both set in Stage Management.
     *
     * @return Collection<int, LeadStage>
     */
    public function all(): Collection
    {
        if ($this->all === null) {
            $this->all = LeadStage::withTrashed()
                ->with('stageGroup')
                ->get()
                ->sortBy([
                    fn (LeadStage $a, LeadStage $b) => $a->trashed() <=> $b->trashed(),
                    fn (LeadStage $a, LeadStage $b) => ($a->stageGroup?->sort_order ?? PHP_INT_MAX) <=> ($b->stageGroup?->sort_order ?? PHP_INT_MAX),
                    fn (LeadStage $a, LeadStage $b) => ($a->group_id ?? 0) <=> ($b->group_id ?? 0),
                    fn (LeadStage $a, LeadStage $b) => $a->sort_order <=> $b->sort_order,
                    fn (LeadStage $a, LeadStage $b) => $a->id <=> $b->id,
                ])
                ->values();
        }

        return $this->all;
    }

    /** @return Collection<int, LeadStage> */
    public function active(): Collection
    {
        return $this->all()->filter(fn (LeadStage $stage) => !$stage->trashed())->values();
    }

    /**
     * Every stage value leads.status may hold (deleted stages included,
     * draft stages not - those live in leads.draft_stage).
     *
     * @return array<int, string>
     */
    public function keys(): array
    {
        return $this->all()->reject(fn (LeadStage $stage) => $stage->is_draft)->pluck('key')->values()->all();
    }

    /**
     * Draft stages, key => name, deleted ones included (labels, and
     * which leads.draft_stage values count as a draft stage).
     *
     * @return array<string, string>
     */
    public function draftLabels(): array
    {
        return $this->all()->filter(fn (LeadStage $stage) => $stage->is_draft)->pluck('name', 'key')->all();
    }

    /**
     * The active draft stages offered in the Save dialog - to $user's
     * role (every one for Admin / Super Admin) and $productId when given.
     *
     * @return array<string, string>
     */
    public function draftsFor(?User $user = null, ?int $productId = null): array
    {
        return $this->active()
            ->filter(fn (LeadStage $stage) => $stage->is_draft && $this->offered($stage, $user, $productId))
            ->pluck('name', 'key')
            ->all();
    }

    private function offered(LeadStage $stage, ?User $user, ?int $productId): bool
    {
        if ($productId !== null && !$stage->isForProduct($productId)) {
            return false;
        }

        return $user === null || $user->isAdminOrAbove() || $stage->isForRole((int) $user->role_id);
    }

    /** @return array<string, string> key => name, deleted stages included */
    public function labels(): array
    {
        return $this->all()->pluck('name', 'key')->all();
    }

    /** @return array<string, string> key => group colour slug, deleted stages included */
    public function slugs(): array
    {
        return $this->all()->mapWithKeys(fn (LeadStage $stage) => [$stage->key => $stage->groupSlug()])->all();
    }

    /**
     * Active stages (not draft stages) as group name => [key => name],
     * in group Order, then stage Order.
     *
     * @param  callable(LeadStage): bool|null  $filter
     * @return array<string, array<string, string>>
     */
    public function grouped(?callable $filter = null): array
    {
        $groups = [];

        foreach ($this->active() as $stage) {
            if ($stage->is_draft || ($filter && !$filter($stage))) {
                continue;
            }

            $groups[$stage->groupName()][$stage->key] = $stage->name;
        }

        return $groups;
    }

    /**
     * The active stages $user may pick: those assigned to their role,
     * or every stage for Admin / Super Admin - for $productId's leads
     * when given.
     *
     * @return array<string, array<string, string>>
     */
    public function groupedFor(User $user, ?int $productId = null): array
    {
        return $this->grouped(fn (LeadStage $stage) => $this->offered($stage, $user, $productId));
    }
}
