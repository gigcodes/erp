<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MagentoProductCommonError implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
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
            'count',
            'Message',
        ];
    }

    //START - Purpose : Set width - DEVTASK-20123
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
            },
        ];
    }
    //END - DEVTASK-20123
}
