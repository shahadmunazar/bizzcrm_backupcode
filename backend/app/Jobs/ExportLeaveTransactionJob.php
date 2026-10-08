<?php
namespace App\Jobs;
use App\Models\User;
use App\Models\Box;
use App\Models\LeaveType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class ExportLeaveTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $boxId;
    protected $type;
    protected $fileName;
    protected $filters;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $boxId, $type, $fileName, $filters = [])
    {
        $this->userId = $userId;
        $this->boxId = $boxId;
        $this->type = $type;
        $this->fileName = $fileName;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::find($this->userId);
            $box = Box::find($this->boxId);

            if (!$user || !$box) {
                Log::error('ExportLeaveTransactionJob: User or Box not found.');
                return;
            }

            $boxUser = \App\Models\BoxUser::where('user_id', $this->userId)
                ->where('box_id', $this->boxId)
                ->first();

            if (!$boxUser) {
                Log::error('ExportLeaveTransactionJob: BoxUser not found.');
                return;
            }

            // Construct Query
            $query = \App\Models\LeaveTransaction::with(['leaveType', 'leaveRequest'])
                ->where('box_user_id', $boxUser->id);

            // Apply Filters
            if (!empty($this->filters['year'])) {
                $query->whereYear('date', $this->filters['year']);
            }
            // Date Range Filter (if provided string "dd/mm/yyyy - dd/mm/yyyy")
            if (!empty($this->filters['dateRange'])) {
                $dates = explode(' - ', $this->filters['dateRange']);
                if (count($dates) === 2) {
                    try {
                        $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('date', [$startDate, $endDate]);
                    } catch (\Exception $e) {
                         // Ignore invalid date format
                    }
                }
            }
            // Leave Types Filter
            if (!empty($this->filters['leaveTypes']) && is_array($this->filters['leaveTypes']) && count($this->filters['leaveTypes']) > 0) {
                 $query->whereHas('leaveType', function ($q) {
                    $q->whereIn('leave_type', $this->filters['leaveTypes']);
                 });
            }
            // Credit/Debit Filter
            if (!empty($this->filters['creditDebitFilters']) && is_array($this->filters['creditDebitFilters']) && count($this->filters['creditDebitFilters']) > 0) {
                 $query->where(function ($q) {
                     $filters = $this->filters['creditDebitFilters'];
                    if (in_array('credit', $filters)) {
                        $q->orWhere('credit_leave', '>', 0);
                    }
                    if (in_array('debit', $filters)) {
                        $q->orWhere('debit_leave', '>', 0);
                    }
                 });
            }

            $transactions = $query->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($row) {
                    return [
                        'date'                => \Carbon\Carbon::parse($row->date)->format('d M Y'),
                        'applied_date'        => $row->applied_date ? \Carbon\Carbon::parse($row->applied_date)->format('d M Y') : ($row->leaveRequest ? \Carbon\Carbon::parse($row->leaveRequest->created_at)->format('d M Y') : '-'),
                        'approved_date'       => $row->approved_date ? \Carbon\Carbon::parse($row->approved_date)->format('d M Y') : (($row->leaveRequest && $row->leaveRequest->status === 'approved') ? \Carbon\Carbon::parse($row->leaveRequest->updated_at)->format('d M Y') : ($row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y') : '-')),
                        'leave_type'          => $row->leaveType?->leave_type ?? '-',
                        'transaction_remarks' => $row->transaction_remarks,
                        'credit_leave'        => $row->credit_leave,
                        'debit_leave'         => $row->debit_leave,
                        'lapsed_leave'        => $row->lapsed_leave ?? 0,
                        'carried_forward_leave'=> $row->carried_forward_leave ?? 0,
                        'balance_leave'       => $row->balance_leave,
                    ];
                });

            $data = $transactions->toArray();
            $storagePath = 'exports/' . $this->fileName; // public disk relative path

            if ($this->type === 'csv') {
                $fileContent = fopen('php://temp', 'r+');
                // Header
                fputcsv($fileContent, ['Date', 'Applied Date', 'Approved Date', 'Leave Type', 'Transaction Remarks', 'Credit', 'Debit', 'Lapsed', 'C.F.', 'Balance']);
                
                foreach ($data as $row) {
                    fputcsv($fileContent, [
                        $row['date'],
                        $row['applied_date'],
                        $row['approved_date'],
                        $row['leave_type'],
                        $row['transaction_remarks'],
                        $row['credit_leave'],
                        $row['debit_leave'],
                        $row['lapsed_leave'],
                        $row['carried_forward_leave'],
                        $row['balance_leave']
                    ]);
                }
                
                rewind($fileContent);
                $content = stream_get_contents($fileContent);
                fclose($fileContent);
                
                Storage::disk('public')->put($storagePath, $content);

            } elseif ($this->type === 'pdf') {
                 // We need a view 'exports.leave_transaction_pdf' for PDF
                 $pdf = Pdf::loadView('exports.leave_transaction_pdf', [
                    'transactions' => $data,
                    'user' => $user,
                    'box' => $box
                ]);
                Storage::disk('public')->put($storagePath, $pdf->output());
            }

        } catch (\Exception $e) {
            Log::error('ExportLeaveTransactionJob failed: ' . $e->getMessage());
        }
    }
}
