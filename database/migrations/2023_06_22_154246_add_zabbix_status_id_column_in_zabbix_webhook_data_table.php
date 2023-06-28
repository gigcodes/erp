<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZabbixStatusIdColumnInZabbixWebhookDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->integer("zabbix_status_id")->nullable()->after('event_id');
            $table->text("remarks")->nullable()->after('zabbix_status_id');
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
            $table->dropColumn("zabbix_status_id");
            $table->dropColumn("remarks");
        });
    }
}
