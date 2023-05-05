<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationIdInGitMigrationErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('git_migration_error_logs', function (Blueprint $table) {
            $table->unsignedBigInteger("github_organization_id")->nullable()->after('repository_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('git_migration_error_logs', function (Blueprint $table) {
            //
        });
    }
}
