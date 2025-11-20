<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $expires_in;

    public function __construct($otp, $expires_in)
    {
        $this->otp = $otp;
        $this->expires_in = $expires_in;
    }

    public function build()
    {
        return $this->subject('Password Reset OTP')
                    ->view('emails.driver-password-otp');
    }
}
