<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to Admin, Super Admin and MIS User when a lead is published -
 * i.e. it moves from Draft to Open and is ready to be picked up, or,
 * for an AU Savers lead, is submitted to pricing (Lead Submitted to
 * Pricing) and is ready for MIS to price. See
 * LeadWorkflowService::notifyLeadsPublished() for who receives it and
 * when.
 *
 * One notification per publish action:
 *  - a single lead: its details, and a link to it;
 *  - a Multiple Site batch: one notification for the whole batch,
 *    linking to site #1;
 *  - a CSV import: one summary for every lead the file published
 *    (in-app only, and linking to the leads listing).
 */
class LeadPublishedNotification extends Notification
{
    public const TYPE = 'lead_published';
    public const EVENT = 'published';

    /**
     * @param Lead $lead The lead published - for a batch, site #1; for
     *   an import summary, any one of them.
     * @param int $count Leads published by this action - the number of
     *   sites for a Multiple Site batch, rows for an import.
     */
    public function __construct(
        protected Lead $lead,
        protected User $actor,
        protected int $count = 1,
        protected bool $imported = false
    ) {
    }

    /**
     * "database" first so a mail outage never costs the in-app
     * notification. An import summary isn't emailed - it would
     * arrive alongside the importer's own confirmation and adds
     * nothing a glance at the listing doesn't.
     */
    public function via($notifiable): array
    {
        return $this->imported ? ['database'] : ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        $data = [
            'type' => self::TYPE,
            'event' => self::EVENT,
            'title' => $this->title(),
            'message' => $this->message(),
            'count' => $this->count,
            'product_name' => $this->lead->product?->name,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_role' => $this->actor->role?->name,
        ];

        // An import summary covers many leads - it has no single lead
        // to point at (NotificationController::open() sends it to the
        // leads listing instead).
        if (!$this->imported) {
            $data += [
                'lead_id' => $this->lead->id,
                'lead_display_id' => $this->lead->display_id,
                'lead_name' => $this->leadName(),
            ];
        }

        return $data;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title() . ': ' . LeadAssignedNotification::leadLabel($this->lead))
            ->view('emails.lead-assigned', [
                'lead' => $this->lead,
                'badge' => $this->submittedToPricing() ? 'Submitted to Pricing' : 'Lead Open',
                'title' => $this->title(),
                'messageText' => $this->message(),
                'url' => route('leads.show', $this->lead),
                'assignee' => $notifiable,
            ]);
    }

    private function title(): string
    {
        if ($this->submittedToPricing()) {
            return $this->count > 1 ? 'Leads Submitted to Pricing' : 'Lead Submitted to Pricing';
        }

        return $this->count > 1 ? 'New Leads Open' : 'New Lead Open';
    }

    /**
     * An AU Savers lead is published as Lead Submitted to Pricing - it
     * is waiting for MIS to price it rather than simply Open.
     */
    private function submittedToPricing(): bool
    {
        return $this->lead->requiresPricing();
    }

    /**
     * e.g. "Riya Sharma (Account Executive) published Lead #1500 -
     * Acme Ltd (AU Savers). It is now Open and ready to be assigned."
     */
    private function message(): string
    {
        $role = $this->actor->role?->name;
        $who = $role ? "{$this->actor->name} ({$role})" : $this->actor->name;
        $product = $this->lead->product?->name;
        $for = $product ? " for {$product}" : '';

        if ($this->imported) {
            $leads = $this->count === 1 ? '1 lead' : "{$this->count} leads";
            $verb = $this->count === 1 ? 'It is' : 'They are';

            return $this->submittedToPricing()
                ? "{$who} imported {$leads}{$for} and submitted " . ($this->count === 1 ? 'it' : 'them') . " to pricing. {$verb} ready for pricing."
                : "{$who} imported and published {$leads}{$for}. {$verb} now Open.";
        }

        $name = $this->leadName();
        $label = filled($name) ? " - {$name}" : '';
        $product = $product ? " ({$product})" : '';

        if ($this->count > 1) {
            $base = $this->lead->base_lead_id ?? $this->lead->display_id;
            $sites = "{$this->count} site leads (#{$base}-1 to #{$base}-{$this->count})";

            return $this->submittedToPricing()
                ? "{$who} submitted Multiple Site Lead #{$base}{$label}{$product} to pricing as {$sites}. They are ready for pricing" . $this->assignedSuffix() . '.'
                : "{$who} published Multiple Site Lead #{$base}{$label}{$product} as {$sites}. They are now Open" . $this->nextStep() . '.';
        }

        return $this->submittedToPricing()
            ? "{$who} submitted Lead #{$this->lead->display_id}{$label}{$product} to pricing. It is ready for pricing" . $this->assignedSuffix() . '.'
            : "{$who} published Lead #{$this->lead->display_id}{$label}{$product}. It is now Open" . $this->nextStep() . '.';
    }

    /**
     * " and has been assigned to Jane Doe" when publishing handed the
     * lead to its intended Account Manager (imported leads).
     */
    private function assignedSuffix(): string
    {
        return $this->lead->isWithAccountManager() && $this->lead->assignee
            ? " and has been assigned to {$this->lead->assignee->name}"
            : '';
    }

    /**
     * A lead published with an intended Account Manager (imported
     * leads) is handed to them straight away - say so rather than
     * asking for an assignment that has already happened.
     */
    private function nextStep(): string
    {
        return $this->assignedSuffix() ?: ' and ready to be assigned';
    }

    private function leadName(): ?string
    {
        return $this->lead->company_business_name ?? $this->lead->customer_name;
    }
}
