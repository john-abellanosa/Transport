<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendDefaultPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;      // add this
    public $password;

    // Update the constructor
    public function __construct($name, $email, $password)
    {
        $this->name = $name;
        $this->email = $email;   // assign email
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Your Default Password')
                    ->view('admin.emails.default_password');
    }
}
