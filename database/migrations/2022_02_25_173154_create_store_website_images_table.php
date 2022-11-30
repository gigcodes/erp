<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->default('0');
            $table->integer('category_id')->default('0');
            $table->integer('media_id')->default('0');
            $table->string('media_type')->default('');
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
        Schema::dropIfExists('store_website_images');
    }
}
