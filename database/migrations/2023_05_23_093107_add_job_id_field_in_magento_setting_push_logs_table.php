<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobIdFieldInMagentoSettingPushLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_setting_push_logs', function (Blueprint $table) {
            $table->string("job_id")->nullable()->after('setting_id');
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
            $table->dropColumn("job_id");
        });
    }
}
