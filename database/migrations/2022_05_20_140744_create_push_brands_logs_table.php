<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushBrandsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_brands_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('error_type')->nullable();
            $table->longText('error')->nullable();
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
        Schema::dropIfExists('push_brands_logs');
    }
}
