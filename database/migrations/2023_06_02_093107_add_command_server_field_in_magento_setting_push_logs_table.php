<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommandServerFieldInMagentoSettingPushLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_setting_push_logs', function (Blueprint $table) {
            $table->string("command_server")->nullable()->after('command_output');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_setting_push_logs', function (Blueprint $table) {
            $table->dropColumn("command_server");
        });
    }
}
