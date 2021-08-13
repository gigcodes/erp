<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\product_discount_excel_file;
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
        
        product_discount_excel_file::truncate();
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

