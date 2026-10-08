<?php
namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $email, $otp, $otpExpiresAt;

    public function __construct($email, $otp, $otpExpiresAt)
    {
        $this->email        = $email;
        $this->otp          = $otp;
        $this->otpExpiresAt = $otpExpiresAt;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new OtpMail($this->otp, $this->otpExpiresAt));
    }
}
