<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThirdPartyLeadsSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        Schema::disableForeignKeyConstraints();
        DB::connection()->disableQueryLog();

        $tables = [
            'india_mart_leads',
            'trade_india_leads',
            'trade_xl_leads',
            'pharma_hoper_leads',
            'pharma_khabar_leads',
            'pharma_vend_leads',
        ];

        $this->command->info("========================================================");
        $this->command->info("🚀 Starting Third-Party Leads Data Import...");
        $this->command->info("========================================================");

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->command->warn("⚠️ Target table {$table} does not exist. Skipping...");
                continue;
            }

            $this->command->info("📂 Processing Table: {$table}");
            $startTime = microtime(true);

            $stream = DB::connection('bizzcrm_migrate')->table($table)->cursor();
            $chunk = [];
            $pendingLeads = [];
            $leadsChunk = [];
            $processed = 0;
            $batchSize = 2000;

            foreach ($stream as $item) {
                $row = (array) $item;
                $processed++;

                $mappedRow = $this->mapApiTableRow($table, $row);
                if (!empty($mappedRow)) {
                    $chunk[] = $mappedRow;
                }

                // Prepare pending leads
                if ($row['status'] === 'Pending' || empty($row['lead_id'])) {
                    $newLead = $this->mapLeadData($table, $row);
                    if ($newLead) {
                        $newLead['created_at'] = $row['created_at'] ?? now();
                        $newLead['updated_at'] = $row['updated_at'] ?? now();
                        $newLead['created_from'] = 'API';
                        $newLead['box_id'] = $row['box_id'] ?? 1;
                        $newLead['lead_source_id'] = $row['lead_source_id'] ?? null;
                        
                        if (empty($newLead['name'])) {
                            $newLead['name'] = 'Unknown API Lead';
                        }
                        
                        $pendingLeads[] = $newLead;
                    }
                }

                if (count($chunk) >= $batchSize) {
                    try {
                        DB::table($table)->insertOrIgnore($chunk);
                    } catch (\Exception $e) {
                        file_put_contents(public_path('seeder_error.log'), $e->getMessage());
                        throw $e;
                    }
                    $chunk = [];

                    if (count($pendingLeads) > 0) {
                        $phones = array_filter(array_column($pendingLeads, 'mobile'));
                        $emails = array_filter(array_column($pendingLeads, 'email'));

                        $existingPhones = empty($phones) ? [] : DB::table('leads')->whereIn('phone', $phones)->pluck('phone')->toArray();
                        $existingEmails = empty($emails) ? [] : DB::table('leads')->whereIn('email', $emails)->pluck('email')->toArray();

                        foreach ($pendingLeads as $pLead) {
                            $phoneExists = !empty($pLead['mobile']) && in_array($pLead['mobile'], $existingPhones);
                            $emailExists = !empty($pLead['email']) && in_array($pLead['email'], $existingEmails);

                            if (!$phoneExists && !$emailExists) {
                                $leadsChunk[] = $pLead;
                            }
                        }

                        if (count($leadsChunk) > 0) {
                            try {
                                DB::table('leads')->insertOrIgnore($leadsChunk);
                            } catch (\Exception $e) {
                                file_put_contents(public_path('seeder_error.log'), $e->getMessage());
                                throw $e;
                            }
                        }
                        
                        $pendingLeads = [];
                        $leadsChunk = [];
                    }
                }
            }

            // Process remainders
            if (count($chunk) > 0) {
                try {
                    DB::table($table)->insertOrIgnore($chunk);
                } catch (\Exception $e) {
                    file_put_contents(public_path('seeder_error.log'), $e->getMessage());
                    throw $e;
                }
            }

            if (count($pendingLeads) > 0) {
                $phones = array_filter(array_column($pendingLeads, 'mobile'));
                $emails = array_filter(array_column($pendingLeads, 'email'));

                $existingPhones = empty($phones) ? [] : DB::table('leads')->whereIn('phone', $phones)->pluck('phone')->toArray();
                $existingEmails = empty($emails) ? [] : DB::table('leads')->whereIn('email', $emails)->pluck('email')->toArray();

                foreach ($pendingLeads as $pLead) {
                    $phoneExists = !empty($pLead['mobile']) && in_array($pLead['mobile'], $existingPhones);
                    $emailExists = !empty($pLead['email']) && in_array($pLead['email'], $existingEmails);

                    if (!$phoneExists && !$emailExists) {
                        $leadsChunk[] = $pLead;
                    }
                }

                if (count($leadsChunk) > 0) {
                    try {
                        DB::table('leads')->insertOrIgnore($leadsChunk);
                    } catch (\Exception $e) {
                        file_put_contents(public_path('seeder_error.log'), $e->getMessage());
                        throw $e;
                    }
                }
            }

            $time = round(microtime(true) - $startTime, 2);
            $this->command->info("✅ Finished {$table} | Total Records: " . number_format($processed) . " | Time: {$time}s");
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info("========================================================");
        $this->command->info("🎉 All Third-Party Leads Imported Successfully");
        $this->command->info("========================================================");
    }

    private function mapApiTableRow($table, $row)
    {
        $mapped = [
            'lead_api_box_id' => $row['lead_api_box_id'] ?? 1,
            'box_id' => $row['box_id'] ?? 1,
            'lead_id' => $row['lead_id'] ?? null,
            'lead_source_id' => $row['lead_source_id'] ?? null,
            'status' => in_array($row['status'] ?? 'Pending', ['Pending', 'Imported', 'Skipped']) ? ($row['status'] ?? 'Pending') : 'Pending',
            'created_at' => $row['created_at'] ?? now(),
            'updated_at' => $row['updated_at'] ?? now(),
        ];

        switch ($table) {
            case 'india_mart_leads':
                $mapped['query_id'] = $row['query_id'] ?? (string) \Illuminate\Support\Str::uuid();
                $mapped['name'] = $row['sendername'] ?? null;
                $mapped['email'] = $row['senderemail'] ?? null;
                $mapped['phone'] = $row['mob'] ?? null;
                $mapped['company_name'] = $row['glusr_usr_companyname'] ?? null;
                $mapped['query'] = $row['enq_message'] ?? null;
                $mapped['city'] = $row['enq_city'] ?? null;
                $mapped['state'] = $row['enq_state'] ?? null;
                $mapped['country'] = $row['country_iso'] ?? null;
                $mapped['date_time_re'] = $row['date_time_re'] ?? null;
                $mapped['query_type'] = $row['qtype'] ?? null;
                break;

            case 'trade_india_leads':
                $mapped['rfi_id'] = $row['rfi_id'] ?? null;
                $mapped['sender_name'] = $row['sender_name'] ?? null;
                $mapped['sender_email'] = $row['sender_email'] ?? null;
                $mapped['sender_mobile'] = $row['sender_mobile'] ?? null;
                $mapped['sender_company'] = $row['sender_co'] ?? null;
                $mapped['message'] = $row['message'] ?? null;
                $mapped['sender_city'] = $row['sender_city'] ?? null;
                $mapped['sender_state'] = $row['sender_state'] ?? null;
                $mapped['sender_country'] = $row['sender_country'] ?? null;
                $mapped['product_name'] = $row['product_name'] ?? null;
                $mapped['submit_time'] = $row['submit_time'] ?? null;
                break;

            case 'trade_xl_leads':
                $mapped['enquiry_id'] = $row['enquiry_id'] ?? null;
                $mapped['contact_person_name'] = $row['contact_person_name'] ?? null;
                $mapped['email_id'] = $row['email_id'] ?? null;
                $mapped['mobile_no'] = $row['mobile_no'] ?? null;
                $mapped['company_name'] = $row['company_name'] ?? null;
                $mapped['message'] = $row['message'] ?? null;
                $mapped['city_state'] = $row['city_state'] ?? null;
                $mapped['product'] = $row['product'] ?? null;
                $mapped['submit_time'] = $row['submit_time'] ?? null;
                break;

            case 'pharma_hoper_leads':
                $mapped['query_id'] = $row['query_id'] ?? null;
                $mapped['name'] = $row['name'] ?? null;
                $mapped['email'] = $row['email'] ?? null;
                $mapped['phone'] = $row['phone'] ?? null;
                $mapped['firm_name'] = $row['firm_name'] ?? null;
                $mapped['message'] = $row['message'] ?? null;
                $mapped['gst_number'] = $row['gst_number'] ?? null;
                $mapped['drug_license'] = $row['drug_license'] ?? null;
                $mapped['address'] = $row['address'] ?? null;
                $mapped['submit_time'] = $row['submit_time'] ?? null;
                break;

            case 'pharma_khabar_leads':
                $mapped['query_id'] = $row['query_id'] ?? null;
                $mapped['name'] = $row['person_name'] ?? null;
                $mapped['email'] = $row['email'] ?? null;
                $mapped['phone'] = $row['mobile'] ?? null;
                $mapped['query'] = $row['requirement'] ?? null;
                $mapped['drugs_license'] = $row['drugs_license'] ?? null;
                $mapped['city'] = $row['city'] ?? null;
                $mapped['state'] = $row['state'] ?? null;
                $mapped['submit_time'] = $row['submit_time'] ?? null;
                break;

            case 'pharma_vend_leads':
                $mapped['query_id'] = $row['query_id'] ?? null;
                $mapped['name'] = $row['name'] ?? null;
                $mapped['email'] = $row['email'] ?? null;
                $mapped['phone'] = $row['phone'] ?? null;
                $mapped['companyname'] = $row['companyname'] ?? null;
                $mapped['message'] = $row['message'] ?? null;
                $mapped['gst_number'] = $row['gst_number'] ?? null;
                $mapped['drug_license'] = $row['drug_license'] ?? null;
                $mapped['location'] = $row['location'] ?? null;
                $mapped['state'] = $row['state'] ?? null;
                $mapped['submit_time'] = $row['submit_time'] ?? null;
                break;
        }

        return $mapped;
    }

    private function mapLeadData($table, $row)
    {
        $lead = [];
        switch ($table) {
            case 'india_mart_leads':
                $lead['name'] = $row['sendername'] ?? null;
                $lead['email'] = $row['senderemail'] ?? null;
                $lead['mobile'] = $row['mob'] ?? null;
                $lead['company_name'] = $row['glusr_usr_companyname'] ?? null;
                $lead['query'] = $row['enq_message'] ?? null;
                $lead['location'] = ($row['enq_city'] ?? '') . ' ' . ($row['enq_state'] ?? '');
                break;

            case 'trade_india_leads':
                $lead['name'] = $row['sender_name'] ?? null;
                $lead['email'] = $row['sender_email'] ?? null;
                $lead['mobile'] = $row['sender_mobile'] ?? null;
                $lead['company_name'] = $row['sender_co'] ?? null;
                $lead['query'] = $row['message'] ?? null;
                $lead['location'] = ($row['sender_city'] ?? '') . ' ' . ($row['sender_state'] ?? '');
                break;

            case 'trade_xl_leads':
                $lead['name'] = $row['contact_person_name'] ?? null;
                $lead['email'] = $row['email_id'] ?? null;
                $lead['mobile'] = $row['mobile_no'] ?? null;
                $lead['company_name'] = $row['company_name'] ?? null;
                $lead['query'] = $row['message'] ?? null;
                $lead['location'] = $row['city_state'] ?? null;
                break;

            case 'pharma_hoper_leads':
                $lead['name'] = $row['name'] ?? null;
                $lead['email'] = $row['email'] ?? null;
                $lead['mobile'] = $row['phone'] ?? null;
                $lead['company_name'] = $row['firm_name'] ?? null;
                $lead['query'] = $row['message'] ?? null;
                $lead['gstin'] = $row['gst_number'] ?? null;
                $lead['drug_license'] = $row['drug_license'] ?? null;
                $lead['location'] = $row['address'] ?? null;
                break;

            case 'pharma_khabar_leads':
                $lead['name'] = $row['person_name'] ?? null;
                $lead['email'] = $row['email'] ?? null;
                $lead['mobile'] = $row['mobile'] ?? null;
                $lead['query'] = $row['requirement'] ?? null;
                $lead['drug_license'] = $row['drugs_license'] ?? null;
                $lead['location'] = ($row['city'] ?? '') . ' ' . ($row['state'] ?? '');
                break;

            case 'pharma_vend_leads':
                $lead['name'] = $row['name'] ?? null;
                $lead['email'] = $row['email'] ?? null;
                $lead['mobile'] = $row['phone'] ?? null;
                $lead['company_name'] = $row['companyname'] ?? null;
                $lead['query'] = $row['message'] ?? null;
                $lead['gstin'] = $row['gst_number'] ?? null;
                $lead['drug_license'] = $row['drug_license'] ?? null;
                $lead['location'] = ($row['location'] ?? '') . ' ' . ($row['state'] ?? '');
                break;

            default:
                return null;
        }
        
        return $lead;
    }
}
