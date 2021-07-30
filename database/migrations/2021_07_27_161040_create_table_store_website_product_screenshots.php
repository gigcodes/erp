<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStoreWebsiteProductScreenshots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('store_website_product_screenshots',function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->string('sku')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->string('store_website_name')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('store_website_product_screenshots');
    }
}
