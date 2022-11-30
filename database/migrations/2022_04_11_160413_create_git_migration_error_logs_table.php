<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitMigrationErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_migration_error_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('repository_id')->nullable();
            $table->text('branch_name')->nullable();
            $table->text('ahead_by')->nullable();
            $table->text('behind_by')->nullable();
            $table->string('last_commit_author_username')->nullable();
            $table->string('last_commit_time')->nullable();
            $table->longText('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('git_migration_error_logs');
    }
}
