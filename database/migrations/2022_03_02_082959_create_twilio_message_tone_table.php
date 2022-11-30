<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioMessageToneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_message_tones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->nullable();
            $table->string('end_work_ring')->nullable();
            $table->string('intro_ring')->nullable();
            $table->string('busy_ring')->nullable();
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
        Schema::dropIfExists('twilio_message_tone');
    }
}
