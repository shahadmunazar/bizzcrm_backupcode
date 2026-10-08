<?php

namespace App\Services;

use App\Models\Box;
use App\Models\FacebookForm;
use App\Models\FacebookLead;
use App\Models\FacebookPage;
use App\Helpers\WhatsApp;
use Illuminate\Support\Facades\Log;

class FacebookLeadService
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function sync(Box $box, ?int $userId = null)
    {
        Log::info('FacebookLeadService sync started', [
            'box_id' => $box->id,
            'box_slug' => $box->slug
        ]);

        try {
            $whatsapp = new WhatsApp($box);
            $pages = FacebookPage::where('box_id', $box->id)->get();
            Log::info("FacebookLeadService: Found {$pages->count()} pages for box {$box->id}");

            $totalNewLeadsFound = 0;
            $processedLeads = 0;
            $skippedUnmapped = 0;
            $errors = [];

            foreach ($pages as $page) {
                $forms = FacebookForm::where('facebook_page_id', $page->id)->get();
                foreach ($forms as $form) {
                    try {
                        Log::info("FacebookLeadService: Starting download for form {$form->name} ({$form->form_id})");
                        
                        // 1. Download new leads from Facebook API to FacebookLead table
                        $whatsapp->downloadLeads($box, $form);
                        // Update total_leads count in facebook_forms table
                        $totalLeads = FacebookLead::where('form_id', $form->form_id)->where('box_id', $box->id)->count();
                        Log::info("FacebookLeadService: Download finished for form {$form->name}. Found total of {$totalLeads} leads in database.");
                        $form->update([
                            'total_leads' => $totalLeads,
                            'updated_by' => $userId ?? ($box->superadmin_id ?? null)
                        ]);
                        
                        Log::info("FacebookLeadService: Updated total_leads for form {$form->name} to {$totalLeads}");
                    } catch (\Exception $e) {
                        Log::error("FacebookLeadService: Error downloading leads for form {$form->form_id}", [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $errors[] = "Error downloading leads for form {$form->name}: " . $e->getMessage();
                    }
                }
            }

            // 2. Process all 'Pending' leads from FacebookLead table to CRM leads table
            $pendingLeads = FacebookLead::where('box_id', $box->id)
                ->where('status', 'Pending')
                ->get();

            $totalNewLeadsFound = $pendingLeads->count();

            foreach ($pendingLeads as $fbLead) {
                try {
                    $form = FacebookForm::where('form_id', $fbLead->form_id)
                        ->where('box_id', $box->id)
                        ->first();

                    if ($form && $form->is_mapped === 'Yes') {
                        $mapping = $form->mapping;
                        $internalMapping = $mapping['internal'] ?? [];

                        if (!empty($internalMapping)) {
                            // Fetch full lead data if not already present
                            $lead_data = $fbLead->data;
                            if (empty($lead_data) || !isset($lead_data['field_data'])) {
                                $lead_data = $whatsapp->getLead($box, $fbLead->leadgen_id);
                                if ($lead_data) {
                                    $fbLead->data = $lead_data;
                                    $fbLead->save();
                                }
                            }

                            if ($lead_data) {
                                $field_data = $lead_data['field_data'] ?? [];
                                $flatData = [];
                                
                                // Get questions mapping to resolve IDs
                                $questions = $mapping['questions'] ?? [];
                                $nameToId = [];
                                foreach ($questions as $q) {
                                    if (isset($q['key']) && isset($q['id'])) {
                                        $nameToId[$q['key']] = $q['id'];
                                    }
                                }

                                foreach ($field_data as $fd) {
                                    $val = $fd['values'][0] ?? null;
                                    $fbName = $fd['name'];
                                    
                                    // Store by name (key)
                                    $flatData[$fbName] = $val;
                                    
                                    // Also store by ID if we can resolve it
                                    if (isset($nameToId[$fbName])) {
                                        $flatData[$nameToId[$fbName]] = $val;
                                    }
                                }

                                Log::info("FacebookLeadService: Processing lead {$fbLead->leadgen_id} for form {$form->name}", [
                                    'resolved_flat_data_keys' => array_keys($flatData),
                                    'mapping_keys' => array_keys($internalMapping)
                                ]);

                                // Prepare comprehensive filterData for saveLead
                                $facebookParams = $box->facebook_api_params;
                                $filterData = [
                                    'lead_source_id' => $facebookParams['lead_source_id'] ?? null,
                                    'lead_status_id' => $facebookParams['lead_status_id'] ?? null,
                                    'has_words' => $box->has_words ? explode(',', $box->has_words) : [],
                                    'auto_trash' => $box->auto_trash ? explode(',', $box->auto_trash) : [],
                                    'auto_unassigned' => $box->auto_unassigned ? explode(',', $box->auto_unassigned) : [],
                                    'round_robin' => $box->round_robin_auto ?? 0,
                                    'round_robin_users' => $box->round_robin_users ?? [],
                                ];

                                $submit_time = date("Y-m-d H:i:s", strtotime($fbLead->created_time));

                                // saveLead handles the actual mapping using $internalMapping
                                $crmLead = $this->leadService->saveLead(
                                    $box,
                                    $flatData,
                                    $filterData,
                                    $internalMapping,
                                    $submit_time,
                                    $box->superadmin_id,
                                    'Facebook Sync'
                                );

                                if ($crmLead) {
                                    $fbLead->status = 'Processed';
                                    $fbLead->lead_id = $crmLead->id; // Link to the created CRM lead
                                    $fbLead->updated_by = $userId ?? ($box->superadmin_id ?? null);
                                    $fbLead->save();
                                    $processedLeads++;
                                    Log::info("FacebookLeadService: Successfully processed lead {$fbLead->leadgen_id} to CRM Lead ID: {$crmLead->id}");
                                } else {
                                    Log::warning("FacebookLeadService: saveLead returned null for lead {$fbLead->leadgen_id}");
                                }
                            }
                        } else {
                            $skippedUnmapped++;
                        }
                    } else {
                        $skippedUnmapped++;
                    }
                } catch (\Exception $e) {
                    Log::error("FacebookLeadService: Error processing pending lead {$fbLead->leadgen_id}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('FacebookLeadService completed successfully', [
                'box_id' => $box->id,
                'new_leads_found' => $totalNewLeadsFound,
                'leads_processed' => $processedLeads,
                'skipped_unmapped' => $skippedUnmapped,
                'errors_count' => count($errors)
            ]);

            // Create a summary activity for the box
            $description = "Facebook Lead Synchronization Summary:\n";
            $description .= "• Total New Leads Found: {$totalNewLeadsFound}\n";
            $description .= "• Leads Processed & Saved: {$processedLeads}\n";
            $description .= "• Leads Skipped (No Mapping): {$skippedUnmapped}\n";
            
            if (!empty($errors)) {
                $description .= "\nErrors:\n" . implode("\n", $errors);
            }

            \App\Models\Activity::create([
                'box_id' => $box->id,
                'activity_id' => 0, // General box activity
                'type' => 'history',
                'subject' => 'Facebook Leads Sync Completed',
                'description' => $description,
                'activity_date' => now(),
                'added_by' => $box->superadmin_id,
                'last_updated_by' => $box->superadmin_id,
                'status' => 'Close',
                'completed_date' => now(),
                'created_from' => 'Facebook Sync'
            ]);
            
        } catch (\Exception $e) {
            Log::error('FacebookLeadService failed with exception', [
                'box_id' => $box->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
