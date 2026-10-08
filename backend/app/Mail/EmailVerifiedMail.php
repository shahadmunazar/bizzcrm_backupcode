<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appName = config('app.name');
        return $this->subject("Email Successfully Verified - $appName")
            ->view('emails.verification_success')
            ->with([
                'name' => $this->user->name,
                'appName' => $appName,
            ]);
    }
}
