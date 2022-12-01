<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioCallStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_call_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_sid');
            $table->string('call_sid');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('twilio_credentials_id')->nullable();
            $table->integer('customer_website_id')->nullable();
            $table->integer('twilio_number_website_id')->nullable();
            $table->string('customer_number', 20)->nullable();
            $table->string('twilio_number', 20)->nullable();
            $table->string('customer_country', 20)->nullable();
            $table->string('twilio_number_country', 20)->nullable();
            $table->integer('call_duration')->default(0);
            $table->float('call_costing', 8, 5)->default(0);
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
        Schema::dropIfExists('twilio_call_statistics');
    }
}
