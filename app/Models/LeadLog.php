<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * An immutable audit-trail entry for a Lead (and, by extension, its
 * notes/documents and reminders). Rows are only ever created - never
 * updated or deleted - by App\Services\LeadLogger.
 */
class LeadLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'lead_id',
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
