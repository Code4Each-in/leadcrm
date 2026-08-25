<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    // use Queueable;

    protected $user;
    protected $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // ensure relations are loaded (safe for queue)
        $this->user->load(['role', 'agency']);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Welcome')
            ->view('emails.user_created', [
                'user' => $this->user,
                'password' => $this->password,
                'agency' => $this->user->agency,
                'loginUrl' => route('login'),
            ]);
    }
}
