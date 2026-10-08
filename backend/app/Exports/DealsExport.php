<?php

namespace App\Exports;

use App\Models\Deal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DealsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $boxId;
    protected $startDate;
    protected $endDate;

    public function __construct($boxId, $startDate = null, $endDate = null)
    {
        $this->boxId = $boxId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Deal::with(['project', 'contact', 'creator', 'pipeline', 'dealStatus', 'dealPeriod', 'dealSource', 'dealPriority'])->where('box_id', $this->boxId);
        
        if ($this->startDate && $this->endDate) {
            $startDate = date('Y-m-d 00:00:00', strtotime($this->startDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($this->endDate));
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Deal Name',
            'Pipeline',
            'Stage',
            'Deal Value',
            'Contact Name',
            'Project Name',
            'Period',
            'Source',
            'Priority',
            'Tags',
            'Due Date',
            'Expected Close Date',
            'Owner',
            'Status',
            'Created At'
        ];
    }

    public function map($deal): array
    {
        // Format Deal Value
        $formattedValue = $deal->deal_value ? $deal->currency . ' ' . number_format((float)$deal->deal_value, 2) : '---';

        // Format Tags
        $formattedTags = '---';
        if ($deal->tags) {
            $tags = is_string($deal->tags) ? json_decode($deal->tags, true) : $deal->tags;
            if (is_array($tags) && count($tags) > 0) {
                $tagNames = \App\Models\DealTag::whereIn('id', $tags)->pluck('name')->toArray();
                $formattedTags = implode(', ', $tagNames);
            }
        }

        // Format Owner / Assignees
        $formattedOwner = '---';
        if ($deal->assignees) {
            $assignees = is_string($deal->assignees) ? json_decode($deal->assignees, true) : $deal->assignees;
            if (is_array($assignees) && count($assignees) > 0) {
                $userNames = \App\Models\User::whereIn('id', $assignees)->pluck('name')->toArray();
                $formattedOwner = implode(', ', $userNames);
            }
        }

        return [
            $deal->id,
            $deal->name,
            $deal->pipeline ? $deal->pipeline->name : '---',
            $deal->stage ?? 'Unassigned',
            $formattedValue,
            $deal->contact ? $deal->contact->name : '---',
            $deal->project ? $deal->project->name : '---',
            $deal->dealPeriod ? $deal->dealPeriod->name : '---',
            $deal->dealSource ? $deal->dealSource->name : '---',
            $deal->dealPriority ? $deal->dealPriority->name : '---',
            $formattedTags,
            $deal->due_date ? date('d M Y', strtotime($deal->due_date)) : '---',
            $deal->expected_close_date ? date('d M Y', strtotime($deal->expected_close_date)) : '---',
            $formattedOwner,
            $deal->dealStatus ? $deal->dealStatus->name : 'Open',
            $deal->created_at ? $deal->created_at->format('d M Y') : '---',
        ];
    }
}
