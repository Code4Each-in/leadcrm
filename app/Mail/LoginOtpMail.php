<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $expiresIn;

    public function __construct($otp, $expiresIn = 10)
    {
        $this->otp = $otp;
        $this->expiresIn = $expiresIn;
    }

    public function build()
    {
        return $this
            ->subject('Your AGILE ONE Login OTP')
            ->view('emails.login-otp');
    }
}
