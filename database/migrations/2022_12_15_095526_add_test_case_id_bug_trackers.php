<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestCaseIdBugTrackers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bug_trackers', function (Blueprint $table) {
            $table->integer('test_case_id')->nullable();
            $table->text('expected_result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bug_trackers', function (Blueprint $table) {
            $table->dropColumn('test_case_id');
            $table->dropColumn('expected_result');
        });
    }
}
