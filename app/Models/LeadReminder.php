<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeadReminder extends Model
{
    protected $table = 'lead_reminders';

    protected $fillable = [
        'user_id',
        'agency_id',
        'lead_id',
        'date',
        'time',
        'notes',
        'is_triggered',
        'dismissed_at'
    ];

    /*
    |-----------------------------
    | Relationships
    |-----------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /*
    |-----------------------------
    | Casts (IMPORTANT IMPROVEMENT)
    |-----------------------------
    */

    protected $casts = [
        'date' => 'date',
        'time' => 'string',
        'is_triggered' => 'boolean',
    ];
    public function getDateTimeAttribute()
    {
        return Carbon::parse(
            $this->date->format('Y-m-d') . ' ' . $this->time
        );
    }
}
