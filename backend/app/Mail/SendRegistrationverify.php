<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRegistrationverify extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $tempPassword;
    public string $remember_token;
    // public string $token_expires_at;

    public function __construct(
        string $name,
        string $email,
        string $tempPassword,
        string $remember_token,
        // string $token_expires_at
    ) {
        $this->name             = $name;
        $this->email            = $email;
        $this->tempPassword     = $tempPassword;
        $this->remember_token   = $remember_token;
        // $this->token_expires_at = $token_expires_at;
    }

    public function build()
    {
        return $this->subject('Your CRM Account Login Details')
            ->view('emails.verify')
            ->with([
                'name'           => $this->name,
                'email'          => $this->email,
                'password'       => $this->tempPassword,
                'token'          => $this->remember_token,
                'verificationUrl'=> route('verify.email', ['token' => $this->remember_token, 'p' => encrypt($this->tempPassword)]),
                // 'tokenExpiresAt' => $this->token_expires_at,
            ]);
    }
}
