<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogListMagentoExport implements FromArray, ShouldAutoSize
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return  $this->data;
    }
}
