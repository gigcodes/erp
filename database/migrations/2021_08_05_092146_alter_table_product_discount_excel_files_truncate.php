<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ProductDiscountExcelFile;
use App\SupplierDiscountLogHistory;
use App\SupplierBrandDiscount;



class AlterTableProductDiscountExcelFilesTruncate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        ProductDiscountExcelFile::truncate();
        SupplierDiscountLogHistory::truncate();
        SupplierBrandDiscount::truncate();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

