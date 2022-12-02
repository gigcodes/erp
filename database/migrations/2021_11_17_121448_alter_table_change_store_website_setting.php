<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableChangeStoreWebsiteSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `magento_setting_push_logs` CHANGE `command` `command` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
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
