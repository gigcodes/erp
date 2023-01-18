<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_message_delivery_logs', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('marketing_message_customer_id');
			$table->integer('customer_id');
			$table->string('account_sid',191);
			$table->string('message_sid',191);
			$table->string('to',20);
			$table->string('from',20);
			$table->string('delivery_status',25)->nullable();
			$table->string('api_version',20)->nullable();
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
        Schema::dropIfExists('twilio_message_delivery_logs');
    }
};
