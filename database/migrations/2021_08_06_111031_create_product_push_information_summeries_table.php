<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPushInformationSummeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_push_information_summeries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->nullable()->index();
            $table->integer('category_id')->nullable()->index();
            $table->integer('store_website_id')->nullable()->index();
            $table->unsignedSmallInteger('product_push_count')->nullable();
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
        Schema::dropIfExists('product_push_information_summeries');
    }
}
