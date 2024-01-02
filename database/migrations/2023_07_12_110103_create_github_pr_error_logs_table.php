<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubPrErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_pr_error_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->text('log');
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->bigInteger('pull_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_pr_error_logs');
    }
}
