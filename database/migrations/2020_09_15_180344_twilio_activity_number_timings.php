<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TwilioActivityNumberTimings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('twilio_active_numbers_timings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twilio_active_number_id')->default(0);
            $table->integer('day');
            $table->time('morning_start', 0); 
            $table->time('morning_end', 0); 
            $table->time('evening_start', 0); 
            $table->time('evening_end', 0); 
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('twilio_active_numbers_timings');
    }
}
