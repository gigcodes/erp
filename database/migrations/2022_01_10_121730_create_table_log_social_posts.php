<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLogSocialPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_post_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->integer('post_id')->nullable();
            $table->string('platform')->nullable();
            $table->longText('log_title')->nullable();
            $table->longText('log_description')->nullable();
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
        Schema::dropIfExists('instagram_logs');
    }
}
