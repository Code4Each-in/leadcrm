<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOtpNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $loginUser;
    public string $otp;
    public Carbon $generatedAt;
    public string $deviceType;
    public string $userAgent;
    public ?string $ipAddress;

    public function __construct(User $loginUser, string $otp, Carbon $generatedAt, string $deviceType, string $userAgent, ?string $ipAddress)
    {
        $this->loginUser = $loginUser;
        $this->otp = $otp;
        $this->generatedAt = $generatedAt;
        $this->deviceType = $deviceType;
        $this->userAgent = $userAgent;
        $this->ipAddress = $ipAddress;
    }

    public function build()
    {
        return $this
            ->subject('Login OTP Generated - ' . $this->loginUser->name)
            ->view('emails.admin-otp-notification');
    }
}
