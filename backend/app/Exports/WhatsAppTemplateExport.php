<?php

namespace App\Exports;

use App\Models\WhatsAppTemplate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WhatsAppTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    protected $boxId;

    public function __construct($boxId)
    {
        $this->boxId = $boxId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return WhatsAppTemplate::where('box_id', $this->boxId)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Meta ID',
            'Category',
            'Language',
            'Status',
            'Created At',
            'Approved At'
        ];
    }

    public function map($template): array
    {
        return [
            $template->id,
            $template->name,
            $template->whats_app_id,
            $template->category,
            $template->language,
            $template->whats_app_status,
            $template->created_at ? $template->created_at->format('Y-m-d H:i:s') : '---',
            $template->approved_at ? $template->approved_at->format('Y-m-d H:i:s') : '---',
        ];
    }
}
