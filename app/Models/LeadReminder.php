<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadReminder extends Model
{
    protected $fillable = [
        'lead_id',
        'created_by',
        'reminder_date',
        'reminder_time',
        'note',
    ];

    protected $casts = [
        'reminder_date' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
