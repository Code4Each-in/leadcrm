<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadPricing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'supplier_id',
        'rate_type',
        'contract_term_months',
        'total_eac_kwh',
        'day_consumption_kwh',
        'evening_consumption_kwh',
        'night_consumption_kwh',
        'sc_pence_per_day',
        'unit_rate_pence',
        'night_unit_rate_pence',
        'evening_unit_rate_pence',
        'uplift_pence',
        'annual_spend',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * Stored pricing statuses. Draft and Published are MIS's own
     * one-way publish step; once a lead holding Published pricing is
     * with an Account Manager, they approve or decline it (see
     * LeadWorkflowService::reviewPricing()). Every status other than
     * Draft is locked - a change is always a new pricing record.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_DECLINED => 'Declined',
    ];

    protected $casts = [
        'total_eac_kwh' => 'decimal:2',
        'day_consumption_kwh' => 'decimal:2',
        'evening_consumption_kwh' => 'decimal:2',
        'night_consumption_kwh' => 'decimal:2',
        'sc_pence_per_day' => 'decimal:4',
        'unit_rate_pence' => 'decimal:4',
        'night_unit_rate_pence' => 'decimal:4',
        'evening_unit_rate_pence' => 'decimal:4',
        'uplift_pence' => 'decimal:4',
        'annual_spend' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDeclined(): bool
    {
        return $this->status === self::STATUS_DECLINED;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }

    public function isMultiRate(): bool
    {
        return $this->rate_type === 'multi';
    }
}
