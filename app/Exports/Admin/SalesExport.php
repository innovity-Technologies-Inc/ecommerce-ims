<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        protected array $data,
        protected array $headings,
        protected string $title = 'Sales Report'
    ) {}

    public function array(): array
    {
        return collect($this->data)->map(function ($row) {
            // Convert row to collection if it's an object or array
            return collect($row)->map(function ($value) {
                // Robust check for 'empty' values that should be 0 in Excel
                // This covers: null, false, empty strings, and strings with only whitespace
                if ($value === null || $value === false || trim((string) $value) === '') {
                    return 0;
                }

                return $value;
            })->toArray();
        })->toArray();
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
