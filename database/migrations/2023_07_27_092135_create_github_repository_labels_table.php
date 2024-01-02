<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubRepositoryLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_repository_labels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->string('label_name');
            $table->string('label_color')->nullable();
            $table->string('message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_repository_labels');
    }
}
