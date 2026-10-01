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
    public const ACTION_PRICING_APPROVED = 'pricing_approved';
    public const ACTION_PRICING_DECLINED = 'pricing_declined';
    public const ACTION_PRICING_RESUBMITTED = 'pricing_resubmitted';
    public const ACTION_PRICING_PUBLISHED = 'pricing_published';
    public const ACTION_HOLD = 'hold';
    public const ACTION_LOST = 'lost';
    public const ACTION_CLOSED = 'closed';
    // A Lead Staging change (from_status -> to_status). The latest one
    // to STATUS_SENT_BACK names the user who sent the lead back to
    // the AE - see LeadWorkflowService::aeAddedActivity().
    public const ACTION_STAGE_CHANGED = 'stage_changed';

    // Old MIS -> AE -> Account Manager workflow - no longer written,
    // kept so existing history rows keep their labels.
    public const ACTION_PROCESS_STARTED = 'process_started';
    public const ACTION_MOVED_TO_AM = 'moved_to_am';
    public const ACTION_SENT_BACK = 'sent_back';

    /**
     * Pricing review entries - they carry pricing details (decline
     * reasons), so they are only shown to users who can see the
     * lead's pricing (LeadPolicy::viewPricing()).
     */
    public const PRICING_ACTIONS = [
        self::ACTION_PRICING_APPROVED,
        self::ACTION_PRICING_DECLINED,
        self::ACTION_PRICING_RESUBMITTED,
        self::ACTION_PRICING_PUBLISHED,
    ];

    public const ACTION_LABELS = [
        self::ACTION_ASSIGNED => 'Assigned',
        self::ACTION_PRICING_APPROVED => 'Pricing Approved',
        self::ACTION_PRICING_DECLINED => 'Pricing Declined',
        self::ACTION_PRICING_RESUBMITTED => 'Pricing Resubmitted',
        self::ACTION_PRICING_PUBLISHED => 'Pricing Published',
        self::ACTION_REASSIGNED => 'Reassigned',
        self::ACTION_PROCESS_STARTED => 'Start Process',
        self::ACTION_MOVED_TO_AM => 'Moved to Account Manager',
        self::ACTION_SENT_BACK => 'Sent Back to AE',
        self::ACTION_HOLD => 'Put on Hold',
        self::ACTION_LOST => 'Marked Lost',
        self::ACTION_CLOSED => 'Closed',
        self::ACTION_STAGE_CHANGED => 'Stage Changed',
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
