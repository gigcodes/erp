<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusOrderStoreHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_order_statuses_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->integer('store_order_statuses_id')->nullable();
            $table->integer('old_order_status_id')->nullable();
            $table->integer('old_store_website_id')->nullable();
            $table->integer('old_status')->nullable();
            $table->integer('old_store_master_status_id')->nullable();
            $table->integer('new_order_status_id')->nullable();
            $table->integer('new_store_website_id')->nullable();
            $table->integer('new_status')->nullable();
            $table->integer('new_store_master_status_id')->nullable();
            $table->string('action_type')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('store_order_statuses_histories');
    }
}