<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $boxId;

    public function __construct($boxId)
    {
        $this->boxId = $boxId;
    }

    public function collection()
    {
        return Project::with(['projectType', 'projectCategory', 'teamMembers'])->where('box_id', $this->boxId)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Client Name',
            'Priority',
            'Start Date',
            'Due Date',
            'Type',
            'Status',
            'Created At'
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->name,
            $project->client_name ?? 'N/A',
            $project->priority,
            $project->start_date ?? '---',
            $project->due_date ?? '---',
            $project->projectType ? $project->projectType->name : 'N/A',
            $project->status,
            $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : '---',
        ];
    }
}
