<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Everything in the MIS -> AE -> Account Manager workflow after the
 * initial assignment (which keeps its own LeadAssignedNotification):
 * a lead being forwarded to an Account Manager, sent back to an AE,
 * its process being started, it being put on hold, marked lost or
 * closed, or being taken away
 * from someone by a reassignment.
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

    public const EVENT_FORWARDED = 'forwarded';
    public const EVENT_SENT_BACK = 'sent_back';
    public const EVENT_PROCESS_STARTED = 'process_started';
    public const EVENT_CLOSED = 'closed';

    public const EVENT_REASSIGNED = 'reassigned';
    public const EVENT_HOLD = 'hold';
    public const EVENT_LOST = 'lost';

    /**
     * Events that are emailed as well as stored in-app - the ones
     * that hand work over or end the lead's journey. "Process
     * started", "on hold" and "reassigned away from you" are
     * informational only.
     */
    public const MAIL_EVENTS = [
        self::EVENT_FORWARDED,
        self::EVENT_SENT_BACK,
        self::EVENT_LOST,
        self::EVENT_CLOSED,
    ];

    private const TITLES = [
        self::EVENT_REASSIGNED => 'Lead Reassigned',
        self::EVENT_HOLD => 'Lead On Hold',
        self::EVENT_LOST => 'Lead Marked Lost',
        self::EVENT_FORWARDED => 'Lead Forwarded',
        self::EVENT_SENT_BACK => 'Lead Sent Back',
        self::EVENT_PROCESS_STARTED => 'Lead Process Started',
        self::EVENT_CLOSED => 'Lead Closed',
    ];

    public function __construct(
        protected Lead $lead,
        protected User $actor,
        protected string $event,
        protected ?string $note = null,
        // Who the lead went to - only used by EVENT_REASSIGNED.
        protected ?User $newOwner = null
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
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_role' => $this->actor->role?->name,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(self::TITLES[$this->event] . " - Lead #{$this->lead->display_id}")
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
        $actor = $this->actor->name;

        return match ($this->event) {
            self::EVENT_FORWARDED => "{$id} has been forwarded to you by {$actor}.",
            self::EVENT_SENT_BACK => "{$id} has been sent back to you by {$actor}.",
            self::EVENT_PROCESS_STARTED => "{$actor} has started processing {$id}.",
            self::EVENT_CLOSED => "{$id} has been closed by {$actor}.",
            self::EVENT_HOLD => "{$id} has been put on hold by {$actor}.",
            self::EVENT_LOST => "{$id} has been marked as lost by {$actor}.",
            self::EVENT_REASSIGNED => "{$id} has been reassigned from you to " . ($this->newOwner?->name ?? 'another user') . " by {$actor}.",
        };
    }
}
