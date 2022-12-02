<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogStoreWebsiteAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_store_website_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_case_id');
            $table->integer('attribute_id');
            $table->string('attribute_key');
            $table->string('attribute_val');
            $table->integer('store_website_id');
            $table->string('log_msg');
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
        Schema::dropIfExists('log_store_website_attributes');
    }
}
