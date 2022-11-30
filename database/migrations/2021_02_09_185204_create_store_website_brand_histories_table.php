<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteBrandHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_brand_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->enum('type', ['assign', 'remove', 'error'])->default('error');
            $table->text('message')->nullable();
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
        Schema::dropIfExists('store_website_brand_histories');
    }
}
