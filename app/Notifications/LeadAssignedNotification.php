<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to an Account Executive when a lead is assigned to them.
 * Stored for the dashboard (header bell + AE dashboard panel) and
 * emailed. Sent synchronously, like the app's other notifications.
 */
class LeadAssignedNotification extends Notification
{
    public const TYPE = 'lead_assigned';

    public function __construct(
        protected Lead $lead,
        protected User $assigner,
        protected bool $reassigned = false
    ) {
    }

    /**
     * "database" is deliberately first: notifications are sent
     * channel by channel in this order, so if the mail server is
     * down the dashboard notification has already been stored.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => self::TYPE,
            'event' => 'assigned',
            'title' => $this->reassigned ? 'Lead Reassigned' : 'New Lead Assigned',
            'message' => $this->message(),
            // Internal id kept for reference; the link is built from
            // the business-facing display id, which is what
            // leads.show route binding resolves first.
            'lead_id' => $this->lead->id,
            'lead_display_id' => $this->lead->display_id,
            'lead_name' => $this->lead->company_business_name ?? $this->lead->customer_name,
            'assigned_by_id' => $this->assigner->id,
            'assigned_by_name' => $this->assigner->name,
            'assigned_by_role' => $this->assigner->role?->name,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(($this->reassigned ? 'Lead Reassigned' : 'New Lead Assigned') . " - Lead #{$this->lead->display_id}")
            ->view('emails.lead-assigned', [
                'lead' => $this->lead,
                'badge' => $this->reassigned ? 'Lead Reassigned' : 'Lead Assigned',
                'title' => $this->reassigned ? 'Lead Reassigned' : 'New Lead Assigned',
                'messageText' => $this->message(),
                'url' => route('leads.show', $this->lead),
                'assignee' => $notifiable,
            ]);
    }

    /**
     * e.g. "Riya (MIS User) has assigned Lead #1001 to you."
     */
    private function message(): string
    {
        $role = $this->assigner->role?->name;
        $who = $role ? "{$this->assigner->name} ({$role})" : $this->assigner->name;

        $verb = $this->reassigned ? 'reassigned' : 'assigned';

        return "{$who} has {$verb} Lead #{$this->lead->display_id} to you.";
    }
}
