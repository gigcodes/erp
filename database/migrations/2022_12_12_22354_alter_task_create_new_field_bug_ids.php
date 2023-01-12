<?php

use Illuminate\Database\Migrations\Migration;

class AlterTaskCreateNewFieldBugIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (6, "In Progress", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (7, "Completed", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (8, "Discussing", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (9, "Deployed", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (10, "Discuss with Lead", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('ALTER TABLE `tasks` ADD `task_bug_ids` TEXT NULL DEFAULT NULL AFTER `is_flow_task`;');
        DB::statement('ALTER TABLE `site_developments` ADD `bug_id` BIGINT(11) NULL DEFAULT NULL AFTER `website_id`;');
        DB::statement('INSERT INTO `task_categories` (`id`, `parent_id`, `title`, `is_approved`, `deleted_at`, `created_at`, `updated_at`, `is_active`) VALUES (52, 0, "Site Bug", 1, NULL, NULL, NULL, 1);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (6, "In Progress", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (7, "Completed", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (8, "Discussing", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (9, "Deployed", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('INSERT INTO `bug_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (10, "Discuss with Lead", "2022-12-16 15:30:44", "2022-12-16 15:30:44");');
        DB::statement('ALTER TABLE `tasks` ADD `task_bug_ids` TEXT NULL DEFAULT NULL AFTER `is_flow_task`;');
        DB::statement('ALTER TABLE `site_developments` ADD `bug_id` BIGINT(11) NULL DEFAULT NULL AFTER `website_id`;');
        DB::statement('INSERT INTO `task_categories` (`id`, `parent_id`, `title`, `is_approved`, `deleted_at`, `created_at`, `updated_at`, `is_active`) VALUES (52, 0, "Site Bug", 1, NULL, NULL, NULL, 1);');
    }
}
