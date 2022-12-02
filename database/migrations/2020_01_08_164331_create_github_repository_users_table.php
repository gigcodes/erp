<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubRepositoryUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_repository_users', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->integer('github_repositories_id');
            $table->integer('github_users_id');
            $table->string('rights');
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
        Schema::table('github_repository_users', function (Blueprint $table) {
            $table->drop();
        });
    }
}
