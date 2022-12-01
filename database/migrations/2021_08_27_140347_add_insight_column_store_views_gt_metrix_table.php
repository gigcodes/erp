<?php

use Illuminate\Database\Migrations\Migration;

class AddInsightColumnStoreViewsGtMetrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `store_views_gt_metrix` ADD `pagespeed_insight_json` VARCHAR(255) NOT NULL AFTER `pagespeed_json`;');
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
