<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldToWebsiteStoreViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("ALTER TABLE `website_store_views` ADD `store_group_id` INT(10) NULL AFTER `website_store_id`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_store_views', function (Blueprint $table) {
            //
        });
    }
}
