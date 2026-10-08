<?php

namespace App\Jobs;

use App\Exports\WhatsAppCampaignDetailsExport;
use App\Models\User;
use App\Models\WhatsAppCampaign;
use App\Mail\ExportCampaignsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportWhatsAppCampaignDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaignId;
    public $boxId;
    public $userId;
    public $format;

    public function __construct($campaignId, $boxId, $userId, $format = 'excel')
    {
        $this->campaignId = $campaignId;
        $this->boxId = $boxId;
        $this->userId = $userId;
        $this->format = $format;
    }

    public function handle(): void
    {
        try {
            $directory = 'exports/campaigns_details';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $export = new WhatsAppCampaignDetailsExport($this->campaignId, $this->boxId);
            $fileName = 'campaign_details_' . $this->campaignId . '_' . time() . '_' . uniqid();
            
            if ($this->format === 'excel') {
                $fileName .= '.xlsx';
                $filePathRelative = "{$directory}/{$fileName}";
                $fullPath = Storage::disk('public')->path($filePathRelative);
                Excel::store($export, $filePathRelative, 'public');
            } else {
                $fileName .= '.pdf';
                $filePathRelative = "{$directory}/{$fileName}";
                $fullPath = Storage::disk('public')->path($filePathRelative);
                
                $details = $export->collection();
                $headings = $export->headings();
                $data = [];
                foreach ($details as $row) {
                    $data[] = $export->map($row);
                }
                
                $campaign = WhatsAppCampaign::find($this->campaignId);
                
                $pdf = Pdf::loadView('whatsapp.exports.campaign_details_pdf', compact('campaign', 'headings', 'data'));
                $pdf->save($fullPath);
            }
            
            $count = $export->collection()->count();

            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->send(new ExportCampaignsMail($fullPath, $count));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Campaign Details Export Email Failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Campaign Details Export Job Failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
        }
    }
}
