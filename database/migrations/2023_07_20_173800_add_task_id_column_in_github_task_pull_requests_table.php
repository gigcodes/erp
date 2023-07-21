<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaskIdColumnInGithubTaskPullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_task_pull_requests', function (Blueprint $table) {
            $table->dropColumn('github_task_id');
            $table->integer("task_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_task_pull_requests', function (Blueprint $table) {
            //
        });
    }
}
