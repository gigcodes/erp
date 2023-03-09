<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUpdatedAttributeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('product_updated_attribute_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->string('attribute_name')->default('compositions')->index();
            $table->string('attribute_id')->nullable()->index();
            $table->integer('product_id')->index();
            $table->integer('user_id')->index();
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
        //
        Schema::dropIfExists('product_updated_attribute_histories');
    }
}
