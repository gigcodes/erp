<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogExcelImport extends Model
{
    protected $fillable =  array('filename','supplier','number_of_products');
}
