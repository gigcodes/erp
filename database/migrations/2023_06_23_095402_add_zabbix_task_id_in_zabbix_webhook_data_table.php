<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZabbixTaskIdInZabbixWebhookDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->integer('zabbix_task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->dropColumn('zabbix_task_id');
        });
    }
}
