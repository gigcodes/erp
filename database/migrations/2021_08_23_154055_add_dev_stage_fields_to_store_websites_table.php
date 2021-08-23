<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDevStageFieldsToStoreWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("ALTER TABLE `store_websites` ADD `stage_magento_url` VARCHAR(191) NULL AFTER `magento_url`;");
        DB::select("ALTER TABLE `store_websites` ADD `dev_magento_url` VARCHAR(191) NULL AFTER `stage_magento_url`;");
        DB::select("ALTER TABLE `store_websites` ADD `stage_api_token` VARCHAR(191) NULL AFTER `api_token`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            //
        });
    }
}
