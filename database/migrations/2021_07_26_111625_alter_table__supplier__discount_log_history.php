<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSupplierDiscountLogHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_discount_log_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('supplier_brand_discounts_id');
            $table->string('header_name')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_discount_log_history');
    }
}
