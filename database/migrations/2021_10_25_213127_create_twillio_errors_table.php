<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillioErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sid')->nullable();
            $table->string('account_sid')->nullable();
            $table->string('call_sid')->nullable();
            $table->string('error_code')->nullable();
            $table->string('message_text')->nullable();
            $table->string('message_date')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('twilio_errors');
    }
}
