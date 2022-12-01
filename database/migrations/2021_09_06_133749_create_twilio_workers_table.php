<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twilio_credential_id')->nullable();
            $table->integer('twilio_workspace_id')->nullable();
            $table->string('worker_name')->nullable();
            $table->string('worker_sid')->nullable();
            $table->integer('deleted')->default(0)->nullable();
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
        Schema::dropIfExists('twilio_workers');
    }
}
