<?php

namespace App\Exports;

use App\CsvTranslator;
use Maatwebsite\Excel\Concerns\FromCollection;

class CsvTranslatorExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CsvTranslator::select('*')->get();
    }
}
