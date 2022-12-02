<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPriceDiscountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_discount_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->nullable();
            $table->string('product_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('store_website_id')->nullable();
            $table->longText('stage')->nullable();
            $table->string('oparetion')->nullable();
            $table->string('product_price')->nullable();
            $table->string('product_total_price')->nullable();
            $table->string('product_discount')->nullable();
            $table->longText('log')->nullable();
            $table->char('status', '2')->default(1);
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
        Schema::dropIfExists('product_price_discount_logs');
    }
}
