<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterHubstaffTaskIdsIntToBigInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `lead_hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `team_lead_hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `tester_hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `hubstaff_task_notes` MODIFY `task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `tasks` MODIFY `hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `tasks` MODIFY `lead_hubstaff_task_id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `lead_hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `team_lead_hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `developer_tasks` MODIFY `tester_hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `hubstaff_task_notes` MODIFY `task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `tasks` MODIFY `hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
//         DB::statement('ALTER TABLE `tasks` MODIFY `lead_hubstaff_task_id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
    }
}
