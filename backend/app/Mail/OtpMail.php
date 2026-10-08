<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $otpExpiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $otpExpiresAt)
    {
        $this->otp          = $otp;
        $this->otpExpiresAt = $otpExpiresAt;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your OTP Code')
            ->view('emails.otp');
    }
}
