<?php

namespace App\Mail;

use Chatify\Models\Message as ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ChatMessage $chatMessage
    ) {
        $this->chatMessage->loadMissing([
            'sender',
            'conversation.participants.user',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Chat Message - Lead Bridge'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chat.new-message'
        );
    }
}
