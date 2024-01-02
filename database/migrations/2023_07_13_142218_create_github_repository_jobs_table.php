<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubRepositoryJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_repository_jobs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->string('job_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_repository_jobs');
    }
}
