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
        // Explicit format (not a bare 'date' cast) so this serializes
        // to JSON as a plain "Y-m-d" calendar date. A bare 'date' cast
        // serializes through Carbon's UTC ISO-8601 representation -
        // with app.timezone set to Asia/Kolkata (UTC+5:30), midnight
        // on a given day becomes 18:30 the PREVIOUS day in UTC, so
        // the JSON sent to the browser was literally one day behind
        // (e.g. reminder_date=11th -> "...-10T18:30:00.000000Z").
        'reminder_date' => 'date:Y-m-d',
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
