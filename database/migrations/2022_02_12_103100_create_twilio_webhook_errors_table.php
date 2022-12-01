<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioWebhookErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_webhook_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sid');
            $table->text('account_sid');
            $table->text('parent_account_sid');
            $table->string('level');
            $table->string('payload_type');
            $table->text('payload');
            $table->datetime('timestamp');
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
        Schema::dropIfExists('twilio_webhook_errors');
    }
}
