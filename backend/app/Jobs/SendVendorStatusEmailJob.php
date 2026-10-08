<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\VendorApprovedMail;
use App\Mail\VendorRejectedMail;

class SendVendorStatusEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $vendor;
    public $status;
    public $referralCode;

    /**
     * Create a new job instance.
     */
    public function __construct(Vendor $vendor, $status, $referralCode = null)
    {
        $this->vendor = $vendor;
        $this->status = $status;
        $this->referralCode = $referralCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->vendor->user || !$this->vendor->user->email) {
            return;
        }

        $email = $this->vendor->user->email;
        $companyName = $this->vendor->company_name ?? 'Vendor';

        if ($this->status === 'approved') {
            Mail::to($email)->send(new VendorApprovedMail($companyName, $this->referralCode));
        } elseif ($this->status === 'rejected') {
            Mail::to($email)->send(new VendorRejectedMail($companyName, $this->vendor->remarks));
        }
    }
}
