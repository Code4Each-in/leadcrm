<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Everything in the MIS -> Account Manager workflow after the
 * assignment itself (which keeps its own LeadAssignedNotification):
 * MIS publishing pricing (first or updated) for the Account Manager
 * to review, the Account Manager putting the lead on hold, marking it lost or closing it, or the lead being
 * taken away from someone by a reassignment.
 *
 * Every event is stored for the bell/dashboard. Only events that
 * put work in front of someone are also emailed (MAIL_EVENTS below);
 * the rest are in-app FYIs. Sent synchronously like the app's other
 * notifications; "database" is first so a mail outage never costs
 * the in-app notification.
 */
class LeadWorkflowNotification extends Notification
{
    public const TYPE = 'lead_workflow';

    // Approved / Declined are no longer sent (the Account Manager's
    // approve / decline was retired) - kept so stored notifications
    // still render.
    public const EVENT_PRICING_APPROVED = 'pricing_approved';
    public const EVENT_PRICING_DECLINED = 'pricing_declined';
    public const EVENT_PRICING_RESUBMITTED = 'pricing_resubmitted';
    // The lead's first pricing, published after it was assigned.
    public const EVENT_PRICING_PUBLISHED = 'pricing_published';
    public const EVENT_CLOSED = 'closed';

    public const EVENT_REASSIGNED = 'reassigned';
    public const EVENT_HOLD = 'hold';
    public const EVENT_LOST = 'lost';

    // Lead Staging: MIS hands the lead back to its AE (the creator) -
    // Sent Back to AE, or Meter Information - Incorrect/Incomplete -
    // and the AE answers in Notes & Documents or by editing the lead.
    public const EVENT_SENT_BACK_TO_AE = 'sent_back_to_ae';
    public const EVENT_AE_RESPONDED = 'ae_responded';
    public const EVENT_AE_UPDATED_LEAD = 'ae_updated_lead';
    // Someone set the revision stage (Refresh Quotes Requested) by
    // hand - an Account Manager's decline is EVENT_PRICING_DECLINED.
    public const EVENT_REVISION_REQUESTED = 'revision_requested';
    // Any Lead Staging change, to everyone else linked to the lead
    // (in-app only - an FYI; the stages that hand someone work have
    // their own event above).
    public const EVENT_STAGE_CHANGED = 'stage_changed';

    /**
     * Events that are emailed as well as stored in-app - the pricing
     * review (a decline hands work back to MIS, new or updated pricing
     * hands it to the Account Manager) and the end of the lead's
     * journey. "On hold" and "reassigned away from you" are
     * informational only.
     */
    public const MAIL_EVENTS = [
        self::EVENT_PRICING_APPROVED,
        self::EVENT_PRICING_DECLINED,
        self::EVENT_PRICING_RESUBMITTED,
        self::EVENT_PRICING_PUBLISHED,
        self::EVENT_LOST,
        self::EVENT_CLOSED,
        self::EVENT_SENT_BACK_TO_AE,
        self::EVENT_AE_RESPONDED,
        self::EVENT_AE_UPDATED_LEAD,
        self::EVENT_REVISION_REQUESTED,
    ];

    private const TITLES = [
        self::EVENT_REASSIGNED => 'Lead Reassigned',
        self::EVENT_HOLD => 'Lead On Hold',
        self::EVENT_LOST => 'Lead Marked Lost',
        self::EVENT_PRICING_APPROVED => 'Pricing Approved',
        self::EVENT_PRICING_DECLINED => 'Revision Required - Pricing Declined',
        self::EVENT_PRICING_RESUBMITTED => 'Updated Pricing - Review Required',
        self::EVENT_PRICING_PUBLISHED => 'Pricing Available - Review Required',
        self::EVENT_CLOSED => 'Lead Closed',
        self::EVENT_SENT_BACK_TO_AE => 'Lead Sent Back - Action Required',
        self::EVENT_AE_RESPONDED => 'Lead Updated by AE - Review Required',
        self::EVENT_AE_UPDATED_LEAD => 'Lead Edited by AE - Review Required',
        self::EVENT_REVISION_REQUESTED => 'Revision Required - Refresh Quotes Requested',
        self::EVENT_STAGE_CHANGED => 'Lead Stage Updated',
    ];

    public function __construct(
        protected Lead $lead,
        protected User $actor,
        protected string $event,
        protected ?string $note = null,
        // Who the lead went to - only used by EVENT_REASSIGNED.
        protected ?User $newOwner = null,
        // The stage it moved from - only used by EVENT_STAGE_CHANGED
        // (the new one is the lead's status).
        protected ?string $fromStatus = null
    ) {
    }

    public function via($notifiable): array
    {
        return in_array($this->event, self::MAIL_EVENTS, true)
            ? ['database', 'mail']
            : ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => self::TYPE,
            'event' => $this->event,
            'title' => self::TITLES[$this->event],
            'message' => $this->message($notifiable),
            'lead_id' => $this->lead->id,
            'lead_display_id' => $this->lead->display_id,
            'lead_name' => $this->lead->company_business_name ?? $this->lead->customer_name,
            'note' => filled($this->note) ? trim($this->note) : null,
            'status' => $this->lead->status,
            'status_label' => $this->lead->status_label,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_role' => $this->actor->role?->name,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(self::TITLES[$this->event] . ': ' . LeadAssignedNotification::leadLabel($this->lead))
            ->view('emails.lead-assigned', [
                'lead' => $this->lead,
                'badge' => self::TITLES[$this->event],
                'title' => self::TITLES[$this->event],
                'messageText' => $this->message($notifiable),
                'note' => filled($this->note) ? trim($this->note) : null,
                'url' => route('leads.show', $this->lead),
                'assignee' => $notifiable,
            ]);
    }

    private function message($notifiable): string
    {
        $id = "Lead #{$this->lead->display_id}";
        $role = $this->actor->role?->name;
        // Same "Name (Role)" as LeadAssignedNotification.
        $actor = $role ? "{$this->actor->name} ({$role})" : $this->actor->name;

        return match ($this->event) {
            self::EVENT_PRICING_APPROVED => "{$actor} approved the pricing for {$id}.",
            self::EVENT_PRICING_DECLINED => "{$id} requires revision. {$actor} has declined the current pricing. Please review and provide updated pricing/information.",
            self::EVENT_REVISION_REQUESTED => "{$id} requires revision. {$actor} has requested refreshed quotes ({$this->lead->status_label}). Please review and provide updated pricing/information.",
            self::EVENT_PRICING_RESUBMITTED => "{$actor} published updated pricing for {$id}. Please review it.",
            self::EVENT_PRICING_PUBLISHED => "{$actor} published pricing for {$id}. Please review it.",
            self::EVENT_CLOSED => "{$actor} closed {$id}.",
            self::EVENT_HOLD => "{$actor} put {$id} on hold.",
            self::EVENT_LOST => "{$actor} marked {$id} as lost.",
            self::EVENT_SENT_BACK_TO_AE => "{$id} has been sent back to you by {$actor} ({$this->lead->status_label}). Please review the lead, update it and provide the required information.",
            self::EVENT_AE_RESPONDED => "{$id} has been updated by {$actor}. Please review the latest information added to Notes & Documents.",
            self::EVENT_AE_UPDATED_LEAD => "{$actor} has updated {$id}. Please review the changes to the lead.",
            self::EVENT_STAGE_CHANGED => "{$actor} updated the stage of {$id}"
                . ($this->fromStatus ? ' from ' . Lead::statusLabel($this->fromStatus) : '')
                . " to {$this->lead->status_label}.",
            self::EVENT_REASSIGNED => "{$actor} reassigned {$id} from you to " . ($this->newOwner?->name ?? 'another Account Manager') . '.',
        };
    }
}
