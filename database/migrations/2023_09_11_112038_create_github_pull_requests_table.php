<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubPullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_pull_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('pr_number');
            $table->string('repo_name')->nullable();
            $table->bigInteger('github_repository_id')->nullable();
            $table->string('pr_title')->nullable();
            $table->string('pr_url')->nullable();
            $table->string('state')->nullable();
            $table->string('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_pull_requests');
    }
}
