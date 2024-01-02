<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use seo2websites\ErpExcelImporter\ExcelImporter;

class LogExcelImport extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="filename",type="string")
     * @SWG\Property(property="supplier",type="string")
     * @SWG\Property(property="number_of_products",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="website",type="string")
     * @SWG\Property(property="supplier_email",type="string")
     * @SWG\Property(property="md5",type="string")
     */
    use Mediable;

    protected $fillable = ['filename', 'supplier', 'number_of_products', 'status', 'website', 'supplier_email', 'md5', 'message'];

    public function checkIfExcelImporterExist($md5)
    {
        $excelImport = ExcelImporter::where('md5', $md5)->first();
        if ($excelImport) {
            return true;
        } else {
            return false;
        }
    }
}
