<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubPrActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_pr_activities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->bigInteger('pull_number');
            $table->bigInteger('activity_id');
            $table->string('user');
            $table->string('event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_pr_activities');
    }
}
