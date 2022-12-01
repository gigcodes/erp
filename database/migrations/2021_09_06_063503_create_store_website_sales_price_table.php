<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteSalesPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_sales_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->integer('type_id');
            $table->integer('supplier_id')->nullable();
            $table->double('amount', 8, 2);
            $table->string('amount_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('created_by');
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
        Schema::dropIfExists('store_website_sales_prices');
    }
}
