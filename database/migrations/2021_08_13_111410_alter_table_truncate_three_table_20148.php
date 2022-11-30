<?php

use App\ProductDiscountExcelFile;
use App\SupplierBrandDiscount;
use App\SupplierDiscountLogHistory;
use Illuminate\Database\Migrations\Migration;

class AlterTableTruncateThreeTable20148 extends Migration
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
