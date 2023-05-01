<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;

class CustomerNumberImport implements ToModel
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return $row[0];
    }
}
