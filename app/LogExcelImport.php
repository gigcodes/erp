<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class LogExcelImport extends Model
{
    use Mediable;
    protected $fillable =  array('filename','supplier','number_of_products','status','website','supplier_email');
}
