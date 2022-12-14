<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBugTrackerAllColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bug_trackers', function (Blueprint $table) {
            $table->integer('bug_type_id')->nullable()->change();
            $table->text('summary')->nullable()->change();
            $table->integer('bug_environment_id')->nullable()->change();
            $table->integer('bug_severity_id')->nullable()->change();
        });
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
