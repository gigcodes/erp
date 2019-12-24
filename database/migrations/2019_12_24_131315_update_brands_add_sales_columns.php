<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBrandsAddSalesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function(Blueprint $table)
        {
            $table->integer('sales_discount')->after('deduction_percentage')->default(0);
            $table->integer('b2b_sales_discount')->after('deduction_percentage')->default(0);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('sales_discount');
            $table->dropColumn('b2b_sales_discount');
        });
    }
}
