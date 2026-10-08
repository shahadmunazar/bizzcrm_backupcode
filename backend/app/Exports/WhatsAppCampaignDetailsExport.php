<?php

namespace App\Exports;

use App\Models\WhatsAppCampaignNumber;
use App\Models\WhatsAppCampaign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WhatsAppCampaignDetailsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $campaignId;
    protected $boxId;
    protected $variables;

    public function __construct($campaignId, $boxId)
    {
        $this->campaignId = $campaignId;
        $this->boxId = $boxId;
        
        $campaign = WhatsAppCampaign::with('template')->find($campaignId);
        $this->variables = [];
        $template = $campaign->template ?? null;
        if ($template && $template->components && is_array($template->components)) {
            foreach ($template->components as $component) {
                if ($component['type'] === 'BODY' && isset($component['text'])) {
                    preg_match_all('/{{(\d+)}}/', $component['text'], $matches);
                    if (!empty($matches[1])) {
                        $this->variables = array_unique($matches[1]);
                        sort($this->variables);
                    }
                }
            }
        }
        $this->variables = array_values($this->variables);
    }

    public function collection()
    {
        return WhatsAppCampaignNumber::with('lead')
            ->where('whatsapp_campaign_id', $this->campaignId)
            ->where('box_id', $this->boxId)
            ->get();
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Name',
            'Number',
            'Status',
            'Error',
            'Processed At',
        ];
        
        foreach ($this->variables as $var) {
            $headings[] = "Variable {$var}";
        }
        
        return $headings;
    }

    public function map($row): array
    {
        $data = [
            $row->id,
            $row->name,
            $row->number,
            $row->status,
            $row->error_message ?? '',
            $row->processed_at ? $row->processed_at->format('Y-m-d H:i:s') : '---',
        ];
        
        $params = $row->params ?? [];
        foreach ($this->variables as $var) {
            $data[] = $params[$var] ?? '';
        }
        
        return $data;
    }
}
