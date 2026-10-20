<?php

namespace App\Jobs;

use App\Mail\SendVendorReferralInvitationMail;
use App\Models\VendorReferralInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVendorReferralInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $recipientEmail;
    public string $inviterName;
    public string $companyName;
    public string $referralCode;
    public string $referralUrl;
    public ?string $personalMessage;
    public ?string $customSubject;
    public ?int $invitationId;

    public function __construct(
        string $recipientEmail,
        string $inviterName,
        string $companyName,
        string $referralCode,
        string $referralUrl,
        ?string $personalMessage = null,
        ?string $customSubject = null,
        ?int $invitationId = null
    ) {
        $this->recipientEmail  = $recipientEmail;
        $this->inviterName     = $inviterName;
        $this->companyName     = $companyName;
        $this->referralCode    = $referralCode;
        $this->referralUrl     = $referralUrl;
        $this->personalMessage = $personalMessage;
        $this->customSubject   = $customSubject;
        $this->invitationId    = $invitationId;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->recipientEmail)->send(new SendVendorReferralInvitationMail(
                $this->recipientEmail,
                $this->inviterName,
                $this->companyName,
                $this->referralCode,
                $this->referralUrl,
                $this->personalMessage,
                $this->customSubject
            ));

            if ($this->invitationId) {
                VendorReferralInvitation::where('id', $this->invitationId)->update([
                    'last_sent_at' => now(),
                ]);
            }

            Log::info("Vendor referral invitation email successfully dispatched to {$this->recipientEmail}");
        } catch (\Throwable $e) {
            Log::error("Failed to send vendor referral invitation email to {$this->recipientEmail}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
