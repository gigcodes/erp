<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNullColumnsInBuildProcessErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('build_process_error_logs', function (Blueprint $table) {
            $table->bigInteger('project_id')->nullable()->change();
            $table->text('error_message')->nullable()->change();
            $table->string('error_code')->nullable()->change();
            $table->bigInteger('github_organization_id')->nullable()->change();
            $table->bigInteger('github_repository_id')->nullable()->change();
            $table->string('github_branch_state_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('build_process_error_logs', function (Blueprint $table) {
            //
        });
    }
}
