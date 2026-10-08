<?php

namespace App\Jobs;

use App\Models\Box;
use App\Models\Lead;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Activity;
use App\Notifications\NewLeadNotification;

class ProcessWhatsAppCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; 

    protected $campaign;
    protected $boxId;
    protected $phoneCol;
    protected $nameCol;
    protected $shouldCreateLeads;
    protected $addedBy;
    protected $variableMap;
    protected $createdLeadsCount = 0;
    
    // Internal batching state
    protected $rowBuffer = [];
    protected $totalProcessed = 0;
    protected $cachedUser = null;

    public function __construct($campaignId, $boxId, $phoneCol, $nameCol, $shouldCreateLeads, $addedBy, $variableMap = [])
    {
        $this->campaign = WhatsAppCampaign::find($campaignId);
        $this->boxId = $boxId;
        $this->phoneCol = $phoneCol;
        $this->nameCol = $nameCol;
        $this->shouldCreateLeads = $shouldCreateLeads;
        $this->addedBy = $addedBy;
        $this->variableMap = $variableMap;
    }

    public function handle(): void
    {
        ini_set('memory_limit', '1024M');
        
        try {
            if (!$this->campaign || !$this->campaign->file_path) return;

            $filePath = Storage::disk('public')->path($this->campaign->file_path);
            if (!file_exists($filePath)) {
                $this->campaign->update(['status' => 'FAILED', 'params' => ['error' => 'File not found']]);
                return;
            }

            $this->cachedUser = \App\Models\User::find($this->addedBy);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            if (strtolower($extension) === 'csv') {
                $this->processCSV($filePath);
            } else {
                $this->processExcel($filePath);
            }
            
        } catch (\Exception $e) {
            if ($this->campaign) {
                $this->campaign->update([
                    'status' => 'FAILED',
                    'params' => array_merge($this->campaign->params ?? [], ['error' => $e->getMessage()])
                ]);
            }
            \Log::error('WhatsApp Campaign Processing Error: ' . $e->getMessage());
        }
    }

    protected function processCSV($filePath)
    {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $totalRows = 0;
            while (($rowData = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!empty($rowData) && !(count($rowData) === 1 && trim($rowData[0]) === '')) {
                    $totalRows++;
                }
            }
            rewind($handle);

            $this->campaign->update(['total_rows' => $totalRows, 'status' => 'PROCESSING']);

            $rowIndex = 0;
            while (($rowData = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (empty($rowData) || (count($rowData) === 1 && trim($rowData[0]) === '')) {
                    continue; // Skip completely empty trailing lines
                }

                $rowIndex++;
                // Skip header if looks like text
                if ($rowIndex === 1 && !is_numeric(preg_replace('/[^0-9]/', '', $rowData[$this->phoneCol] ?? ''))) {
                    $this->totalProcessed++;
                    continue;
                }

                $this->addToBuffer($rowData);
                if (count($this->rowBuffer) >= rand(50, 150)) {
                    $this->flushBuffer();
                }
            }
            $this->flushBuffer();
            fclose($handle);
            $this->finalizeCampaign($totalRows);
        }
    }

    protected function processExcel($filePath)
    {
        $inputFileType = IOFactory::identify($filePath);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        
        $spreadsheetInfo = $reader->listWorksheetInfo($filePath);
        $totalRows = $spreadsheetInfo[0]['totalRows'] ?? 0;
        $this->campaign->update(['total_rows' => $totalRows, 'status' => 'PROCESSING']);

        $chunkSize = 500;
        $chunkFilter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
            private $start = 0;
            private $end = 0;
            public function setRows($start, $size) { $this->start = $start; $this->end = $start + $size; }
            public function readCell($columnAddress, $row, $worksheetName = '') {
                if ($row == 1) return true;
                return ($row >= $this->start && $row < $this->end);
            }
        };

        for ($startRow = 1; $startRow <= $totalRows; $startRow += $chunkSize) {
            $chunkFilter->setRows($startRow, $chunkSize);
            $reader->setReadFilter($chunkFilter);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            foreach ($worksheet->getRowIterator($startRow, min($startRow + $chunkSize - 1, $totalRows)) as $row) {
                $rowIndex = $row->getRowIndex();
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) { $rowData[] = $cell->getValue(); }

                if ($rowIndex === 1 && !is_numeric(preg_replace('/[^0-9]/', '', $rowData[$this->phoneCol] ?? ''))) {
                    $this->totalProcessed++;
                    continue;
                }

                $this->addToBuffer($rowData);
                if (count($this->rowBuffer) >= rand(50, 150)) {
                    $this->flushBuffer();
                }
            }
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            if (function_exists('gc_collect_cycles')) gc_collect_cycles();
        }
        $this->flushBuffer();
        $this->finalizeCampaign($totalRows);
    }

    protected function addToBuffer($rowData)
    {
        $phone = preg_replace('/[^0-9]/', '', $rowData[$this->phoneCol] ?? '');
        if (strlen($phone) >= 7) {
            $this->rowBuffer[] = [
                'phone' => substr($phone, 0, 35),
                'rowData' => $rowData
            ];
        } else {
            $this->totalProcessed++;
        }
    }

    protected function flushBuffer()
    {
        if (empty($this->rowBuffer)) return;

        $phones = array_unique(array_column($this->rowBuffer, 'phone'));
        $existingLeads = Lead::where('box_id', $this->boxId)
            ->whereIn('phone', $phones)
            ->get()
            ->keyBy('phone');

        $numbersBatch = [];
        $activitiesBatch = [];

        foreach ($this->rowBuffer as $item) {
            $phone = $item['phone'];
            $rowData = $item['rowData'];
            $name = ($this->nameCol !== null && isset($rowData[$this->nameCol])) ? $rowData[$this->nameCol] : ('Imported ' . $phone);
            if (is_object($name)) $name = (string)$name;
            if (!$name) $name = 'Imported ' . $phone;

            $variables = [];
            if (!empty($this->variableMap)) {
                foreach ($this->variableMap as $vIndex => $colIndex) {
                    if ($colIndex !== '' && isset($rowData[$colIndex])) {
                        $variables[$vIndex] = $rowData[$colIndex];
                    }
                }
            }

            $lead = $existingLeads->get($phone);
            $leadId = null;

            if ($this->shouldCreateLeads) {
                if (!$lead) {
                    $lead = Lead::create([
                        'box_id' => $this->boxId,
                        'phone' => $phone,
                        'name' => $name, 
                        'lead_status_id' => 1, 
                        'added_by' => $this->addedBy,
                        'assigned_to' => $this->addedBy,
                        'created_from' => 'WhatsApp Campaign (Excel)'
                    ]);
                    $this->createdLeadsCount++;
                    if ($this->cachedUser) {
                        $this->cachedUser->notify(new NewLeadNotification($lead, 'assigned', 'System', 'WhatsApp Campaign'));
                    }
                } else {
                    $lead->update(['name' => $name]);
                }
                $leadId = $lead->id;
            } else if ($lead) {
                $leadId = $lead->id;
            }

            if ($leadId) {
                $activitiesBatch[] = [
                    'box_id' => $this->boxId,
                    'activity_id' => $leadId,
                    'activity_type' => null,
                    'label' => 'WhatsApp Campaign',
                    'subject' => 'Added to WhatsApp Campaign',
                    'description' => "Contact added to WhatsApp Campaign: {$this->campaign->name}",
                    'type' => 'note',
                    'activity_date' => now(),
                    'assigned_to' => $this->addedBy,
                    'added_by' => $this->addedBy,
                    'last_updated_by' => $this->addedBy,
                    'status' => 'Open',
                    'completed_date' => now(),
                    'created_from' => 'WhatsApp Campaign',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            $numbersBatch[] = [
                'box_id' => $this->boxId,
                'whatsapp_campaign_id' => $this->campaign->id,
                'lead_id' => $leadId,
                'phone' => $phone,
                'name' => $name,
                'status' => 'PENDING',
                'metadata' => !empty($variables) ? json_encode(['variables' => $variables]) : null,
                'added_by' => $this->addedBy,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->totalProcessed++;
        }

        if (!empty($numbersBatch)) WhatsAppCampaignNumber::insert($numbersBatch);
        if (!empty($activitiesBatch)) Activity::insert($activitiesBatch);

        $this->campaign->update(['processed_rows' => $this->totalProcessed]);
        $this->rowBuffer = [];
    }

    protected function finalizeCampaign($totalRows)
    {
        $this->campaign->update([
            'status' => 'IMPORTED',
            'processed_rows' => $this->totalProcessed
        ]);
        
        // Force the processed rows to equal total rows (or the actual processed count)
        $this->campaign->processed_rows = $this->totalProcessed;
        $this->campaign->save();
    }
}
