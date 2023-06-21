<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZabbixWebhookDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zabbix_webhook_data', function (Blueprint $table) {
            $table->id();
            $table->text('subject')->nullable();
            $table->text("message")->nullable();
            $table->dateTime("event_start")->nullable();
            $table->string("event_name")->nullable();
            $table->string("host")->nullable();
            $table->string("severity")->nullable();
            $table->text("operational_data")->nullable();
            $table->integer("event_id")->nullable();
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
        Schema::dropIfExists('zabbix_webhook_data');
    }
}
