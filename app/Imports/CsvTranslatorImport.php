<?php

namespace App\Imports;

use App\CsvTranslator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CsvTranslatorImport implements ToModel, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new CsvTranslator([
            'key' => $row['key'],
            'en' => $row['en'],
            'es' => $row['es'],
            'ru' => $row['ru'],
            'ko' => $row['ko'],
            'ja' => $row['ja'],
            'it' => $row['it'],
            'de' => $row['de'],
            'fr' => $row['fr'],
            'nl' => $row['nl'],
            'zh' => $row['zh'],
            'ar' => $row['ar'],
            'ur' => $row['ur'],
        ]);
    }
}
