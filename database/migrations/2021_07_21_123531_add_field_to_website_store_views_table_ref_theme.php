<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFieldToWebsiteStoreViewsTableRefTheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('website_store_views', 'ref_theme_group_id')) {
            DB::select('ALTER TABLE `website_store_views` ADD `ref_theme_group_id` INT NULL AFTER `store_group_id`;');
        }
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
