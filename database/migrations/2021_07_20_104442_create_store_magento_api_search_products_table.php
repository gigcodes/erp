<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreMagentoApiSearchProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_magento_api_search_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_id')->nullable();
            $table->longText('website')->nullable();
            $table->longText('sku')->nullable();
            $table->longText('size')->nullable();
            $table->longText('brands')->nullable();
            $table->longText('dimensions')->nullable();
            $table->longText('composition')->nullable();
            $table->string('english')->nullable();
            $table->string('arabic')->nullable();
            $table->string('german')->nullable();
            $table->string('spanish')->nullable();
            $table->string('french')->nullable();
            $table->string('italian')->nullable();
            $table->string('japanese')->nullable();
            $table->string('korean')->nullable();
            $table->string('russian')->nullable();
            $table->string('chinese')->nullable();
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
        Schema::dropIfExists('store_magento_api_search_products');
    }
}
