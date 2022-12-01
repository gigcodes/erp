<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexOnDateHubstaffActivitySummaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` ADD INDEX(`user_id`);');
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` ADD INDEX(`date`);');
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` ADD INDEX(`created_at`);');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hubstaff_activity_summaries', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` DROP INDEX `user_id`; ');
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` DROP INDEX `date`; ');
            \DB::statement('ALTER TABLE `hubstaff_activity_summaries` DROP INDEX `created_at`; ');
        });
    }
}
