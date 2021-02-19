<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
use seo2websites\ErpExcelImporter\ExcelImporter;

class LogExcelImport extends Model
{
    use Mediable;
    protected $fillable =  array('filename','supplier','number_of_products','status','website','supplier_email','md5','message');

    public function checkIfExcelImporterExist($md5)
    {
    	$excelImport = ExcelImporter::where('md5',$md5)->first();
    	if($excelImport){
    		return true;
    	}else{
    		return false;
    	}
    }
    
}
