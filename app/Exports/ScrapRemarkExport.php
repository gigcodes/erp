<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScrapRemarkExport implements FromArray, ShouldAutoSize
{
    public function __construct(protected array $remarks)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $remarks = [];
        foreach ($this->remarks as $key => $remark) {
            if ($key == 0) {
                foreach ($remark as $f => $v) {
                    $remarks[$key][$f] = $f;
                }
            }
            $key = $key + 1;

            foreach ($remark as $f => $v) {
                $remarks[$key][$f] = $v;
            }
        }

        return $remarks;
    }
}
