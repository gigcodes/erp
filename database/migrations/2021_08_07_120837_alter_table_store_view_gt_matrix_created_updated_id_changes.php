<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableStoreViewGtMatrixCreatedUpdatedIdChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `store_views_gt_metrix` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
        \DB::statement('ALTER TABLE `store_views_gt_metrix` CHANGE `updated_at` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
        \DB::statement('ALTER TABLE `store_views_gt_metrix` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
    }
}
