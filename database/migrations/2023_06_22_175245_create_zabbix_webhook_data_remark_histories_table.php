<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZabbixWebhookDataRemarkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zabbix_webhook_data_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('zabbix_webhook_data_id');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('zabbix_webhook_data_remark_histories');
    }
}
