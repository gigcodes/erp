<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZabbixWebhookDataStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zabbix_webhook_data_status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('zabbix_webhook_data_id');
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zabbix_webhook_data_status_histories');
    }
}
