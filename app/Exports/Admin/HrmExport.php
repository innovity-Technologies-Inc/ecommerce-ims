<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class HrmExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        protected array $data,
        protected array $headings,
        protected string $title = 'HRM Report'
    ) {}

    public function array(): array
    {
        $exportData = [];
        foreach ($this->data as $row) {
            $newRow = [];
            foreach ($row as $value) {
                if ($value === null || $value === '' || $value === false || (is_string($value) && trim($value) === '')) {
                    $newRow[] = '0';
                } else {
                    $newRow[] = (string) $value;
                }
            }
            $exportData[] = $newRow;
        }

        return $exportData;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
