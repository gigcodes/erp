<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPushInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_push_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->index();
            $table->string('sku')->index();
            $table->string('status')->index();
            $table->unsignedSmallInteger('quantity')->index();
            $table->boolean('stock_status')->default(0)->index();
            $table->softDeletes();
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
        Schema::dropIfExists('product_push_informations');
    }
}