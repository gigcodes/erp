<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer("zabbix_task_id")->nullable();
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
            $table->dropColumn("zabbix_task_id");
        });
    }
}
