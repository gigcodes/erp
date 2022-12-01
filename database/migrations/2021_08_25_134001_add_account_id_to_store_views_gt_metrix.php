<?php

use Illuminate\Database\Migrations\Migration;

class AddAccountIdToStoreViewsGtMetrix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `store_views_gt_metrix` ADD `account_id` VARCHAR(255) NOT NULL AFTER `test_id`;');
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
