<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPushInformationHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_push_information_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->index();
            $table->string('old_sku')->nullable()->index();
            $table->string('sku')->nullable()->index();
            $table->string('old_status')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->unsignedSmallInteger('old_quantity')->nullable()->index();
            $table->unsignedSmallInteger('quantity')->nullable()->index();
            $table->boolean('old_stock_status')->default(0)->nullable()->index();
            $table->boolean('stock_status')->nullable()->index();
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
        Schema::dropIfExists('product_push_information_histories');
    }
}
