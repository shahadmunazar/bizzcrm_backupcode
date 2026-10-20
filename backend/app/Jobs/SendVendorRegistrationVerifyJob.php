<?php

namespace App\Jobs;

use App\Mail\SendVendorRegistrationVerify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVendorRegistrationVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $name;
    protected string $email;
    protected string $tempPassword;
    protected string $remember_token;
    protected ?string $companyName;
    protected ?string $referralCode;

    public function __construct(
        string $name,
        string $email,
        string $tempPassword,
        string $remember_token,
        ?string $companyName = null,
        ?string $referralCode = null
    ) {
        $this->name           = $name;
        $this->email          = $email;
        $this->tempPassword   = $tempPassword;
        $this->remember_token = $remember_token;
        $this->companyName    = $companyName;
        $this->referralCode   = $referralCode;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new SendVendorRegistrationVerify(
                $this->name,
                $this->email,
                $this->tempPassword,
                $this->remember_token,
                $this->companyName,
                $this->referralCode
            ));
        } catch (\Throwable $e) {
            Log::error("Failed to send vendor registration verification email to {$this->email}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
