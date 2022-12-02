<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioCallWaitingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_call_waitings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('call_sid')->nullable();
            $table->string('account_sid')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('twilio_call_waitings');
    }
}
