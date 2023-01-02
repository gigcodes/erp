<?php

use Illuminate\Database\Migrations\Migration;

class IndexBugTrackerTableWebsite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `bug_trackers` ADD INDEX(`website`);');
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
