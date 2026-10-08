<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SummaryExport implements FromArray, WithTitle
{
    protected $summary;

    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function array(): array
    {
        return $this->summary;
    }

    public function title(): string
    {
        return 'Summary';
    }
}

