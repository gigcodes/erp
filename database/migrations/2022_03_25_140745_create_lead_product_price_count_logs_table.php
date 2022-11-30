<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadProductPriceCountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_product_price_count_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->nullable();
            $table->string('product_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->longText('log')->nullable();
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
        Schema::dropIfExists('lead_product_price_count_logs');
    }
}
