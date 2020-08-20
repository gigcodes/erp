<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreWiseLandingPageProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_wise_landing_page_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('landing_page_products_id');
            $table->unsignedInteger('store_website_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_wise_landing_page_products');
    }
}
