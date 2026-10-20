<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVendorReferralInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientEmail;
    public string $inviterName;
    public string $companyName;
    public string $referralCode;
    public string $referralUrl;
    public ?string $personalMessage;
    public ?string $customSubject;

    public function __construct(
        string $recipientEmail,
        string $inviterName,
        string $companyName,
        string $referralCode,
        string $referralUrl,
        ?string $personalMessage = null,
        ?string $customSubject = null
    ) {
        $this->recipientEmail = $recipientEmail;
        $this->inviterName = $inviterName;
        $this->companyName = $companyName;
        $this->referralCode = $referralCode;
        $this->referralUrl = $referralUrl;
        $this->personalMessage = $personalMessage;
        $this->customSubject = $customSubject;
    }

    public function build()
    {
        $subject = $this->customSubject ?: "Exclusive Invitation to join " . config('app.name', 'ZipZapGo') . " as a Vendor Partner";

        return $this->subject($subject)
            ->view('emails.vendor_referral_invitation')
            ->with([
                'recipientEmail'  => $this->recipientEmail,
                'inviterName'     => $this->inviterName,
                'companyName'     => $this->companyName,
                'referralCode'    => $this->referralCode,
                'referralUrl'     => $this->referralUrl,
                'personalMessage' => $this->personalMessage,
            ]);
    }
}
