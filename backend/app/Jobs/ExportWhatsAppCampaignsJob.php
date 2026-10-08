<?php

namespace App\Jobs;

use App\Exports\WhatsAppCampaignExport;
use App\Models\WhatsAppCampaign;
use App\Models\User;
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

class ExportWhatsAppCampaignsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $boxId;
    public $status;
    public $format;
    public $userId;

    public function __construct($boxId, $status, $format, $userId)
    {
        $this->boxId = $boxId;
        $this->status = $status;
        $this->format = $format;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            $directory = 'exports/campaigns';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $fileName = 'campaigns_' . time() . '_' . uniqid();
            
            if ($this->format === 'excel') {
                $fileName .= '.xlsx';
                $filePathRelative = "{$directory}/{$fileName}";
                $fullPath = Storage::disk('public')->path($filePathRelative);
                
                Excel::store(new WhatsAppCampaignExport($this->boxId, $this->status), $filePathRelative, 'public');
            } else {
                $fileName .= '.pdf';
                $filePathRelative = "{$directory}/{$fileName}";
                $fullPath = Storage::disk('public')->path($filePathRelative);
                
                $query = WhatsAppCampaign::with('template')->where('box_id', $this->boxId);
                if ($this->status) {
                    if ($this->status === 'COMPLETED') {
                        $query->whereIn('status', ['COMPLETED', 'FAILED']);
                    } else {
                        $query->where('status', $this->status);
                    }
                }
                $campaigns = $query->get();
                $count = $campaigns->count();
                
                $pdf = Pdf::loadView('whatsapp.exports.campaigns_pdf', compact('campaigns'));
                $pdf->save($fullPath);
            }

            if (!isset($count)) {
                $export = new WhatsAppCampaignExport($this->boxId, $this->status);
                $count = $export->collection()->count();
            }

            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->send(new ExportCampaignsMail($fullPath, $count));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Campaign Export Email Failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Campaign Export Job Failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
        }
    }
}
