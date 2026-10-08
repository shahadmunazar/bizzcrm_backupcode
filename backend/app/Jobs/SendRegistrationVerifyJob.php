<?php
namespace App\Jobs;

use App\Mail\SendRegistrationverify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegistrationVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $name;
    protected string $email;
    protected string $tempPassword;
    protected string $remember_token;
    // protected string $token_expires_at;

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

    public function handle(): void
    {
        Mail::to($this->email)->send(new SendRegistrationverify(
            $this->name,
            $this->email,
            $this->tempPassword,
            $this->remember_token,
            // $this->token_expires_at
        ));
    }
}
