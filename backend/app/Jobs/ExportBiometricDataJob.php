<?php

namespace App\Jobs;

use App\Models\BiometricMachine;
use App\Models\Box;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportBiometricDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $boxId;
    protected $boxSlug;
    protected $type;
    protected $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $boxId, $boxSlug, $type, $fileName)
    {
        $this->userId = $userId;
        $this->boxId = $boxId;
        $this->boxSlug = $boxSlug;
        $this->type = $type;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $machines = BiometricMachine::with(['branch.city', 'branch.state', 'city', 'state'])
            ->where('box_id', $this->boxId)
            ->get();

        $filePath = "exports/{$this->fileName}"; // Relative to disk root

        if ($this->type === 'pdf') {
            // Generate PDF
            $pdf = Pdf::loadView('exports.biometric_pdf', ['machines' => $machines]);
            Storage::disk('public')->put($filePath, $pdf->output());
        } else {
            // Generate CSV (Excel)
            $csvContent = "Machine No,Machine Type,Serial No,Machine Name,Branch,Location,Status,Last Sync\n";
            
            foreach ($machines as $machine) {
                // Determine branch name
                $branchName = $machine->branch ? $machine->branch->name : '-';
                
                // Determine location
                $locationParts = [];
                if ($machine->city) $locationParts[] = $machine->city->name;
                elseif ($machine->branch && $machine->branch->city) $locationParts[] = $machine->branch->city->name;
                
                if ($machine->state) $locationParts[] = $machine->state->name;
                elseif ($machine->branch && $machine->branch->state) $locationParts[] = $machine->branch->state->name;
                
                $location = !empty($locationParts) ? implode(', ', $locationParts) : '-';
                
                // Determine status and last sync
                $status = $machine->sync_status === 'green' ? 'Active' : 'Offline';
                $lastSync = $machine->last_sync ? $machine->last_sync : '-';

                // Construct row
                $row = [
                    $machine->machine_no,
                    $machine->machine_type,
                    $machine->serial_no,
                    $machine->machine_name,
                    $branchName,
                    $location,
                    $status,
                    $lastSync
                ];

                // Escape CSV fields
                $row = array_map(function($field) {
                    $field = (string) $field;
                    // Escape double quotes by doubling them
                    $field = str_replace('"', '""', $field);
                    // Wrap in double quotes if it contains comma, newline, or double quote
                    if (preg_match('/[,"\n\r]/', $field)) {
                        return '"' . $field . '"';
                    }
                    return $field;
                }, $row);

                $csvContent .= implode(',', $row) . "\n";
            }
            
            Storage::disk('public')->put($filePath, $csvContent);
        }

        // Notification removed as per user request
    }
}
