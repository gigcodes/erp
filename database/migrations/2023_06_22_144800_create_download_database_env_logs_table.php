<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownloadDatabaseEnvLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('download_database_env_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('store_website_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('type')->nullable();
            $table->text('cmd')->nullable();
            $table->json('output')->nullable();
            $table->integer('return_var')->nullable();
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
        Schema::dropIfExists('download_database_env_logs');
    }
}
