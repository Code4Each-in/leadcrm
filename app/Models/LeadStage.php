<?php

namespace App\Models;

use App\Support\LeadStages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A Lead Staging stage, managed from Stage Management
 * (LeadStageController). It belongs to a group (LeadStageGroup) and
 * takes its products and colour from it; its Order is its place in that
 * group. `key` is the stored value and never changes after creation;
 * the name, group and roles can. Deleting is a soft delete, so the
 * stage history of past leads keeps its name - and it's refused while
 * any lead is still at the stage.
 *
 * is_draft is set on the seeded Call Back / Awaiting Additional
 * Information only, for the leads' Save dialog (leads.draft_stage) -
 * Stage Management never sets or shows it.
 *
 * Read through App\Support\LeadStages (cached per request), not
 * queried directly.
 */
class LeadStage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'group_id',
        'roles',
        'sort_order',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'roles' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn () => app(LeadStages::class)->flush();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
    }

    /** Its group - deleted ones included, so a retired stage keeps its colour. */
    public function stageGroup(): BelongsTo
    {
        return $this->belongsTo(LeadStageGroup::class, 'group_id')->withTrashed();
    }

    /** @return array<int, int> */
    public function roleIds(): array
    {
        return array_map('intval', $this->roles ?? []);
    }

    /** @return array<int, int> its group's products */
    public function productIds(): array
    {
        return $this->stageGroup?->productIds() ?? [];
    }

    public function isForRole(int $roleId): bool
    {
        return in_array($roleId, $this->roleIds(), true);
    }

    public function isForProduct(int $productId): bool
    {
        return in_array($productId, $this->productIds(), true);
    }

    public function groupName(): string
    {
        return (string) $this->stageGroup?->name;
    }

    public function groupSlug(): string
    {
        return (string) $this->stageGroup?->color;
    }

    /**
     * Leads currently at this stage - deletion is refused while any are.
     * Call Back / Awaiting Additional Information are held in
     * draft_stage on a draft, not in status.
     */
    public function leadsAtStageCount(): int
    {
        return $this->is_draft
            ? Lead::where('status', Lead::STATUS_DRAFT)->where('draft_stage', $this->key)->count()
            : Lead::where('status', $this->key)->count();
    }
}
