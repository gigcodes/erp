<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMagentoSettingPushLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('magento_setting_push_logs',function(Blueprint $table) {
            $table->string("status")->nullable()->after("command");
        });*/
        \DB::statement('ALTER TABLE `magento_setting_push_logs` MODIFY `store_website_id` INTEGER UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
