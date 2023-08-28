<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterActionToGithubActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_actions', function (Blueprint $table) {
            $table->string('github_actor')->nullable()->change();;
            $table->string('github_api_url')->nullable()->change();;
            $table->string('github_base_ref')->nullable()->change();;
            $table->string('github_event_name')->nullable()->change();;
            $table->string('github_job')->nullable()->change();;
            $table->string('github_ref')->nullable()->change();;
            $table->string('github_ref_name')->nullable()->change();;
            $table->string('github_ref_type')->nullable()->change();;
            $table->string('github_repository')->nullable()->change();;
            $table->integer('github_repository_id')->nullable()->change();;
            $table->integer('github_run_attempt')->nullable()->change();;
            $table->integer('github_run_id')->nullable()->change();;
            $table->string('github_workflow')->nullable()->change();;
            $table->string('runner_name')->nullable()->change();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_actions', function (Blueprint $table) {
            //
        });
    }
}
