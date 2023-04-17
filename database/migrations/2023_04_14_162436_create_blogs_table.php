<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('idea', 524)->nullable();
            $table->string('keyword', 524)->nullable();
            $table->text('content')->nullable();
            $table->string('plaglarism', 8)->nullable();
            $table->string('internal_link', 524)->nullable();
            $table->string('external_link', 524)->nullable();
            $table->string('meta_desc', 524)->nullable();
            $table->string('url_structure', 524)->nullable();
            $table->text('url_xml')->nullable();
            $table->dateTime('publish_blog_date')->nullable();
            $table->tinyInteger('no_index')->nullable();
            $table->tinyInteger('no_follow')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('facebook', 256)->nullable();
            $table->dateTime('facebook_date')->nullable();
            $table->string('instagram', 524)->nullable();
            $table->dateTime('instagram_date')->nullable();
            $table->string('twitter', 256)->nullable();
            $table->dateTime('twitter_date')->nullable();
            $table->string('google', 524)->nullable();
            $table->dateTime('google_date')->nullable();
            $table->string('bing', 524)->nullable();
            $table->dateTime('bing_date')->nullable();
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
        Schema::dropIfExists('blogs');
    }
}
