<?php

namespace App\Exports;

use App\Models\WhatsAppCampaign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WhatsAppCampaignExport implements FromCollection, WithHeadings, WithMapping
{
    protected $boxId;
    protected $status;

    public function __construct($boxId, $status = null)
    {
        $this->boxId = $boxId;
        $this->status = $status;
    }

    public function collection()
    {
        $query = WhatsAppCampaign::with('template')->withCount('messages')->where('box_id', $this->boxId);
        
        if ($this->status) {
            if ($this->status === 'COMPLETED') {
                $query->whereIn('status', ['COMPLETED', 'FAILED']);
            } elseif ($this->status === 'ARCHIVED') {
                $query->where('status', 'ARCHIVED'); // Or whatever the logic for archived is, maybe stopped/paused? Let's check.
            } else {
                $query->where('status', $this->status);
            }
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Template',
            'Status',
            'Messages Sent',
            'Schedule At',
            'Created At'
        ];
    }

    public function map($campaign): array
    {
        return [
            $campaign->id,
            $campaign->name,
            $campaign->template ? $campaign->template->name : 'N/A',
            $campaign->status,
            $campaign->messages_count ?? 0,
            $campaign->schedule_at ? $campaign->schedule_at : 'Immediate',
            $campaign->created_at ? $campaign->created_at->format('Y-m-d H:i:s') : '---',
        ];
    }
}
