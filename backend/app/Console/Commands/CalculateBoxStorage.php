<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Box;
use App\Models\BulkUpload;
use App\Models\HrDocument;
use App\Models\LetterHead;
use App\Models\WhatsAppCampaign;
use App\Models\Activity;
use App\Models\Company;
use App\Models\BoxUser;
use App\Models\JobApplicant;
use App\Models\WhatsAppMessage;
use App\Models\Accounts\InvoiceSetting;
use Illuminate\Support\Facades\DB;

class CalculateBoxStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bizzcrm:calculate-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the total storage used by each Box and update the boxes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting storage calculation for all boxes...");
        $boxes = Box::all();
        $bar = $this->output->createProgressBar(count($boxes));
        $disk = Storage::disk('public');

        foreach ($boxes as $box) {
            $totalBytes = 0;

            // 1. Bulk Uploads (has file_size column)
            $totalBytes += BulkUpload::where('box_id', $box->id)->sum('file_size');
            
            // For BulkUpload report_file_path (not stored as size)
            $bulkUploads = BulkUpload::where('box_id', $box->id)->whereNotNull('report_file_path')->get();
            foreach ($bulkUploads as $upload) {
                if ($disk->exists($upload->report_file_path)) {
                    $totalBytes += $disk->size($upload->report_file_path);
                }
            }

            // 2. Activities (file_size stored in JSON params)
            // We use chunking to avoid memory issues if there are millions of activities
            Activity::where('box_id', $box->id)->whereNotNull('params')->chunk(1000, function ($activities) use (&$totalBytes, $disk) {
                foreach ($activities as $activity) {
                    if (isset($activity->params['files']) && is_array($activity->params['files'])) {
                        foreach ($activity->params['files'] as $file) {
                            if (isset($file['file_size'])) {
                                $totalBytes += (int)$file['file_size'];
                            } elseif (isset($file['file_path']) && $disk->exists($file['file_path'])) {
                                $totalBytes += $disk->size($file['file_path']);
                            }
                        }
                    }
                }
            });

            // 3. HR Documents
            $hrDocs = HrDocument::where('box_id', $box->id)->whereNotNull('file_path')->get();
            foreach ($hrDocs as $doc) {
                if ($disk->exists($doc->file_path)) {
                    $totalBytes += $disk->size($doc->file_path);
                }
            }

            // 4. Letter Heads
            $letterHeads = LetterHead::where('box_id', $box->id)->whereNotNull('file_path')->get();
            foreach ($letterHeads as $lh) {
                if ($disk->exists($lh->file_path)) {
                    $totalBytes += $disk->size($lh->file_path);
                }
            }

            // 5. WhatsApp Campaigns
            $campaigns = WhatsAppCampaign::where('box_id', $box->id)->whereNotNull('file_path')->get();
            foreach ($campaigns as $camp) {
                if ($disk->exists($camp->file_path)) {
                    $totalBytes += $disk->size($camp->file_path);
                }
            }

            // 6. Box / Company Logos
            if ($box->company_logo && $disk->exists($box->company_logo)) {
                $totalBytes += $disk->size($box->company_logo);
            }
            
            // PDF Logo in companies
            $companies = Company::where('box_id', $box->id)->whereNotNull('params')->get();
            foreach ($companies as $comp) {
                if (isset($comp->params['pdf_logo']) && $disk->exists($comp->params['pdf_logo'])) {
                    $totalBytes += $disk->size($comp->params['pdf_logo']);
                }
            }

            // 7. Employee Profile Images
            $boxUsers = BoxUser::where('box_id', $box->id)->whereNotNull('profile_image_path')->get();
            foreach ($boxUsers as $bu) {
                if ($disk->exists($bu->profile_image_path)) {
                    $totalBytes += $disk->size($bu->profile_image_path);
                }
            }

            // 8. Job Applicants
            if (class_exists(JobApplicant::class)) {
                $applicants = JobApplicant::where('box_id', $box->id)->get();
                foreach ($applicants as $app) {
                    if ($app->resume && $disk->exists($app->resume)) $totalBytes += $disk->size($app->resume);
                    if ($app->cover_letter && $disk->exists($app->cover_letter)) $totalBytes += $disk->size($app->cover_letter);
                    if ($app->portfolio && $disk->exists($app->portfolio)) $totalBytes += $disk->size($app->portfolio);
                    if ($app->other_document && $disk->exists($app->other_document)) $totalBytes += $disk->size($app->other_document);
                }
            }
            
            // 9. Invoice Settings (Logos)
            $invoiceSettings = InvoiceSetting::where('box_id', $box->id)->whereNotNull('invoice_logo')->get();
            foreach ($invoiceSettings as $invSet) {
                // Remove 'storage/' prefix as it's saved like 'storage/logos/...' but disk is 'public'
                $cleanPath = str_replace('storage/', '', $invSet->invoice_logo);
                if ($disk->exists($cleanPath)) {
                    $totalBytes += $disk->size($cleanPath);
                }
            }
            
            // 10. WhatsApp Messages (Media)
            WhatsAppMessage::where('box_id', $box->id)->whereNotNull('media')->chunk(1000, function ($messages) use (&$totalBytes, $disk) {
                foreach ($messages as $msg) {
                    if (isset($msg->media['path'])) {
                        $cleanPath = str_replace('/storage/', '', $msg->media['path']);
                        if ($disk->exists($cleanPath)) {
                            $totalBytes += $disk->size($cleanPath);
                        }
                    }
                }
            });

            // Save to box
            $box->storage_used_bytes = $totalBytes; // Keep total
            $box->local_storage_used_bytes = $totalBytes; // All current uploads go to local 'public' disk
            $box->aws_storage_used_bytes = 0; // AWS not configured yet, so 0 bytes
            $box->save();

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nStorage calculation completed successfully!");
    }
}
