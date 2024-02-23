<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameActionColumnInGithubPrActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->renameColumn('action', 'event_header');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->renameColumn('event_header', 'action');
        });
    }
}
