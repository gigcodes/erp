<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushProductsJourney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_push_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('log_list_magento_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('condition')->nullable();
            $table->boolean('is_checked')->default(0);
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
    }
}
