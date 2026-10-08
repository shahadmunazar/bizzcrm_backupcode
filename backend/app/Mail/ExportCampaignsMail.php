<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportCampaignsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $count;

    public function __construct($filePath, $count = 0)
    {
        $this->filePath = $filePath;
        $this->count = $count;
    }

    public function build()
    {
        return $this->subject('WhatsApp Campaigns Export Completed (' . $this->count . ' Campaigns)')
            ->view('emails.export_campaigns')
            ->attach($this->filePath);
    }
}
