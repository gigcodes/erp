<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioCallJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_call_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->string('call_sid')->nullable();
            $table->string('account_sid')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('call_entered')->default(0);
            $table->boolean('called_in_working_hours')->default(0);
            $table->boolean('agent_available')->default(0);
            $table->boolean('agent_online')->default(0);
            $table->boolean('call_answered')->default(0);
            $table->boolean('handled_by_chatbot')->default(0);

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
        Schema::dropIfExists('twilio_call_journey');
    }
}
