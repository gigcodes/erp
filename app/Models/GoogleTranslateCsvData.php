<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleTranslateCsvData extends Model
{
    use HasFactory;

    public $table = 'google_file_translate_csv_datas';

    protected $fillable = ['key', 'value' , 'lang_id' ,'google_file_translate_id'];
}
