<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioCallBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_call_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_agent_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('twilio_credentials_id')->nullable();
            $table->integer('customer_website_id')->nullable();
            $table->integer('twilio_number_website_id')->nullable();
            $table->string('customer_number', 20)->nullable();
            $table->string('twilio_number', 20)->nullable();
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
        Schema::dropIfExists('twilio_call_blocks');
    }
}
