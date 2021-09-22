<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMagentoSettingPushLogsAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('magento_setting_push_logs',function(Blueprint $table) {
            $table->string("status")->nullable()->after("command");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('magento_setting_push_logs',function(Blueprint $table) {
            $table->dropField("status");
        });
    }
}
