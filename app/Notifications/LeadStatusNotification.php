<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeadStatusNotification extends Notification
{
    protected $lead;
    protected $type;
    protected $count;

    public function __construct($lead = null, $type = null, $count = null)
    {
        $this->lead = $lead;
        $this->type = $type;
        $this->count = $count;
    }

    public function via($notifiable)
    {
        return ['mail','database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->view('emails.lead-status', [
                'lead' => $this->lead,
                'title' => $this->getTitle(),
                'messageText' => $this->getMessage(),
                'count' => $this->count
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'lead_id' => $this->lead?->id,
            'type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'count' => $this->count,
        ];
    }
    private function getTitle()
    {
        return match ($this->type) {
            'bulk_assign' => 'New Leads Assigned',
            'to_qa' => 'Lead Assigned to QA',
            'to_manager' => 'Lead Assigned to Manager',
            'return_ae' => 'Lead Returned to AE',
            'completed' => 'Lead Completed',
            'lost' => 'Lead Lost',
            'to_ae' => 'New Lead Assigned',
            default => 'Lead Update'
        };
    }

    private function getMessage()
    {
        return match ($this->type) {
            'bulk_assign' => "You have been assigned {$this->count} new leads.",
            'to_qa' => 'A lead has been moved to you.',
            'to_manager' => 'Lead moved to you.',
            'return_ae' => 'Lead has been returned to you.',
            'completed' => 'Lead marked as completed.',
            'lost' => 'Lead marked as lost.',
            'to_ae' => 'A new lead has been assigned to you.',
            default => 'Lead status updated.'
        };
    }
}
