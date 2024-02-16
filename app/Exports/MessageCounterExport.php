<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MessageCounterExport implements FromCollection, WithHeadings
{
    public function __construct(protected $header, protected $data)
    {
    }

    public function headings(): array
    {
        return [
            $this->header,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }
}
