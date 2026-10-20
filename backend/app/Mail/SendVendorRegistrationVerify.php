<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVendorRegistrationVerify extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $tempPassword;
    public string $token;
    public ?string $companyName;
    public ?string $referralCode;

    public function __construct(
        string $name,
        string $email,
        string $tempPassword,
        string $token,
        ?string $companyName = null,
        ?string $referralCode = null
    ) {
        $this->name         = $name;
        $this->email        = $email;
        $this->tempPassword = $tempPassword;
        $this->token        = $token;
        $this->companyName  = $companyName;
        $this->referralCode = $referralCode;
    }

    public function build()
    {
        return $this->subject('Vendor Account Verification & Access - ' . config('app.name'))
            ->view('emails.vendor_verify')
            ->with([
                'name'         => $this->name,
                'email'        => $this->email,
                'password'     => $this->tempPassword,
                'token'        => $this->token,
                'companyName'  => $this->companyName,
                'referralCode' => $this->referralCode,
                'vendorLoginUrl' => route('superadmin.vendors.login'),
                'verificationUrl' => route('verify.email', ['token' => $this->token, 'p' => encrypt($this->tempPassword)]),
            ]);
    }
}
