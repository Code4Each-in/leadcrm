<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One immutable row of a lead's assignment / movement history. Only
 * ever created by App\Services\LeadWorkflowService.
 */
class LeadAssignment extends Model
{
    const UPDATED_AT = null;

    public const ACTION_ASSIGNED = 'assigned';
    public const ACTION_REASSIGNED = 'reassigned';
    public const ACTION_PROCESS_STARTED = 'process_started';
    public const ACTION_MOVED_TO_AM = 'moved_to_am';
    public const ACTION_SENT_BACK = 'sent_back';
    public const ACTION_HOLD = 'hold';
    public const ACTION_LOST = 'lost';
    public const ACTION_CLOSED = 'closed';

    public const ACTION_LABELS = [
        self::ACTION_ASSIGNED => 'Assigned',
        self::ACTION_REASSIGNED => 'Reassigned',
        self::ACTION_PROCESS_STARTED => 'Start Process',
        self::ACTION_MOVED_TO_AM => 'Moved to Account Manager',
        self::ACTION_SENT_BACK => 'Sent Back to AE',
        self::ACTION_HOLD => 'Put on Hold',
        self::ACTION_LOST => 'Marked Lost',
        self::ACTION_CLOSED => 'Closed',
    ];

    protected $fillable = [
        'lead_id',
        'action',
        'performed_by',
        'performed_by_name',
        'performed_by_role',
        'from_user_id',
        'from_user_name',
        'from_role',
        'to_user_id',
        'to_user_name',
        'to_role',
        'from_status',
        'to_status',
        'note',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }
}
