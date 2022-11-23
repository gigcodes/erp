<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHubstaffTablesIdIntToBigInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `hubstaff_activities` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_by_payment_frequencies` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_notifications` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_summaries` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_historys` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_members` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_payment_accounts` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_projects` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_task_efficiency` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_task_notes` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_tasks` MODIFY `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `hubstaff_activities` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_by_payment_frequencies` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_notifications` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_activity_summaries` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_historys` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_members` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_payment_accounts` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_projects` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_task_efficiency` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_task_notes` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE `hubstaff_tasks` MODIFY `id` INT(11) unsigned NOT NULL AUTO_INCREMENT;');
    }
}
