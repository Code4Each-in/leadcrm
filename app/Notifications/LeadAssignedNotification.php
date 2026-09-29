<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to an Account Manager when MIS / Admin assigns (or reassigns)
 * a lead to them. Stored for the dashboard (header bell + dashboard
 * panel) and emailed. Sent synchronously, like the app's other notifications.
 *
 * A lead can be assigned before its pricing is published - the
 * message then says so, and that a separate "Pricing Available"
 * notification follows (LeadWorkflowService::pricingPublished()).
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
            'title' => $this->title(),
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
            ->subject($this->title() . ': ' . self::leadLabel($this->lead))
            ->view('emails.lead-assigned', [
                'lead' => $this->lead,
                'badge' => $this->reassigned ? 'Lead Reassigned' : 'Lead Assigned',
                'title' => $this->title(),
                'messageText' => $this->message(),
                'url' => route('leads.show', $this->lead),
                'assignee' => $notifiable,
            ]);
    }

    private function title(): string
    {
        return $this->reassigned ? 'Lead Reassigned to You' : 'New Lead Assigned';
    }

    /**
     * "Lead #1001 - Acme Ltd" (the business / customer name when there
     * is one) - used where the lead isn't shown alongside, e.g. the
     * email subject.
     */
    public static function leadLabel(Lead $lead): string
    {
        $name = $lead->company_business_name ?? $lead->customer_name;

        return "Lead #{$lead->display_id}" . (filled($name) ? " - {$name}" : '');
    }

    /**
     * e.g. "Riya Sharma (MIS User) assigned Lead #1001 to you."
     */
    private function message(): string
    {
        $role = $this->assigner->role?->name;
        $who = $role ? "{$this->assigner->name} ({$role})" : $this->assigner->name;

        $verb = $this->reassigned ? 'reassigned' : 'assigned';

        $message = "{$who} {$verb} Lead #{$this->lead->display_id} to you.";

        if ($this->pricingPending()) {
            $message .= ' Pricing is not available yet - you can start working on the lead now, and you will be notified when pricing is published.';
        }

        return $message;
    }

    /**
     * A product with a Pricing section, but nothing published yet.
     */
    private function pricingPending(): bool
    {
        return in_array($this->lead->pricingStage(), [Lead::PRICING_STAGE_NONE, Lead::PRICING_STAGE_DRAFT], true);
    }
}
