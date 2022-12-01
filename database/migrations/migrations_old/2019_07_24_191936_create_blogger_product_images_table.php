<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBloggerProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogger_product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->integer('blogger_product_id')->unsigned();
            $table->foreign('blogger_product_id')->references('id')->on('blogger_products')->onDelet('cascade')->onUpdate('cascade');
            $table->text('other')->nullable();
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
        Schema::dropIfExists('blogger_product_images');
    }
}
