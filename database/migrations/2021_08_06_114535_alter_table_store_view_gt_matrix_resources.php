<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableStoreViewGtMatrixResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `store_views_gt_metrix` CHANGE `resources` `resources` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
    }
}
