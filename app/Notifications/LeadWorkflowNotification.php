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
 * to review,
 * the Account Manager approving or declining the pricing, putting
 * the lead on hold, marking it lost or closing it, or the lead being
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

    public const EVENT_PRICING_APPROVED = 'pricing_approved';
    public const EVENT_PRICING_DECLINED = 'pricing_declined';
    public const EVENT_PRICING_RESUBMITTED = 'pricing_resubmitted';
    // The lead's first pricing, published after it was assigned.
    public const EVENT_PRICING_PUBLISHED = 'pricing_published';
    public const EVENT_CLOSED = 'closed';

    public const EVENT_REASSIGNED = 'reassigned';
    public const EVENT_HOLD = 'hold';
    public const EVENT_LOST = 'lost';

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
    ];

    private const TITLES = [
        self::EVENT_REASSIGNED => 'Lead Reassigned',
        self::EVENT_HOLD => 'Lead On Hold',
        self::EVENT_LOST => 'Lead Marked Lost',
        self::EVENT_PRICING_APPROVED => 'Pricing Approved',
        self::EVENT_PRICING_DECLINED => 'Pricing Declined - Action Required',
        self::EVENT_PRICING_RESUBMITTED => 'Updated Pricing - Review Required',
        self::EVENT_PRICING_PUBLISHED => 'Pricing Available - Review Required',
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
            self::EVENT_PRICING_DECLINED => "{$actor} declined the pricing for {$id}. Please review the reason and publish updated pricing.",
            self::EVENT_PRICING_RESUBMITTED => "{$actor} published updated pricing for {$id}. Please review it and approve or decline.",
            self::EVENT_PRICING_PUBLISHED => "{$actor} published pricing for {$id}. Please review it and approve or decline.",
            self::EVENT_CLOSED => "{$actor} closed {$id}.",
            self::EVENT_HOLD => "{$actor} put {$id} on hold.",
            self::EVENT_LOST => "{$actor} marked {$id} as lost.",
            self::EVENT_REASSIGNED => "{$actor} reassigned {$id} from you to " . ($this->newOwner?->name ?? 'another Account Manager') . '.',
        };
    }
}
