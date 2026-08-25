<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadActivityNotification extends Notification
{
    // use Queueable;

    public $lead;
    public $type;
    public $body;

    public function __construct($lead, $type, $body = null)
    {
        $this->lead = $lead;
        $this->type = $type;
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $title = match($this->type) {
            'note_added' => 'New Note Added',
            'note_with_attachment' => 'New Note with Attachment',
            'document_added' => 'New Document Uploaded',
            default => 'Lead Activity'
        };

        return (new MailMessage)
            ->subject($title)
            ->view('emails.lead_activity', [
                'lead' => $this->lead,
                'type' => $this->type,
                'body' => $this->body,
                'user' => $notifiable,
            ]);
    }
}
