<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FileExcelSheetExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected array $report_data, protected $sheet, protected $path)
    {
    }

    public function sheets(): array
    {
    }
}
