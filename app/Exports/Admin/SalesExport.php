<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected array $data,
        protected array $headings,
        protected string $title = 'Sales Report'
    ) {}

    public function array(): array
    {
        return $this->data;
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
