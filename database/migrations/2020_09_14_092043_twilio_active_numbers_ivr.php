<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TwilioActiveNumbersIvr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('twilio_active_numbers_ivr', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twilio_active_number_id')->default(0);
            $table->integer('category_id');
            $table->string('response',255)->nullable();
            $table->enum('active',['0', '1'])->default(1);
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
        Schema::dropIfExists('twilio_active_numbers_ivr');
    }
}
