<?php

namespace App\Models;

use App\Support\LeadStages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A group of Lead Staging stages (Lead, Pricing, Closing ...), managed
 * from Stage Management. Its products are its stages' products, its
 * colour is theirs on the pills, and its Order places it in the Lead
 * Stages dropdown - each group's stages have their own Order inside it.
 * Deleting is a soft delete, refused while it has stages.
 */
class LeadStageGroup extends Model
{
    use SoftDeletes;

    /**
     * The colours a group can have: CSS slug (the stage-* / pill-*
     * classes on the listing, dashboard and Lead Details) => name.
     */
    public const COLORS = [
        'lead' => 'Green',
        'pricing' => 'Amber',
        'tender' => 'Purple',
        'closing' => 'Red',
        'loa' => 'Blue',
        'contracts' => 'Teal',
    ];

    protected $fillable = [
        'name',
        'color',
        'products',
        'sort_order',
    ];

    protected $casts = [
        'products' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn () => app(LeadStages::class)->flush();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(LeadStage::class, 'group_id');
    }

    /** @return array<int, int> */
    public function productIds(): array
    {
        return array_map('intval', $this->products ?? []);
    }
}
