<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $companyName;

    public function __construct($otp, $companyName)
    {
        $this->otp = $otp;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject('Your OTP for Password Reset')
                    ->view('emails.company-otp');
    }
}
