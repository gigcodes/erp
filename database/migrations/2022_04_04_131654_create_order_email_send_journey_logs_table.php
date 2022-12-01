<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEmailSendJourneyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_email_send_journey_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('steps')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('model_type')->nullable();
            $table->string('send_type')->nullable();
            $table->string('seen')->nullable();
            $table->string('from_email')->nullable();
            $table->string('to_email')->nullable();
            $table->string('subject')->nullable();
            $table->string('message')->nullable();
            $table->string('template')->nullable();
            $table->longText('error_msg')->nullable();
            $table->integer('store_website_id')->nullable();
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
        Schema::dropIfExists('order_email_send_journey_logs');
    }
}
