<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmailFailedReport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function __construct(protected array $lists)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->lists;
    }

    public function headings(): array
    {
        return [
            'Id',
            'From Name',
            'Status',
            'Message',
            'Created',
        ];
    }
}
