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
    ];

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

    public function isMultiRate(): bool
    {
        return $this->rate_type === 'multi';
    }
}
