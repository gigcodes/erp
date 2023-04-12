<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYoutubeVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('youtube_channel_id');
            $table->string('channel_id',256);
            $table->string('link', 256);
            $table->string('media_id', 128);
            $table->string('title', 564)->nullable();
            $table->text('description')->nullable();
            $table->int('like_count', 11)->nullable();
            $table->int('view_count', 11)->nullable();
            $table->int('dislike_count', 11)->nullable();
            $table->int('comment_count', 11)->nullable();
            $table->timestamp('create_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youtube_videos', function (Blueprint $table) {
            Schema::dropIfExists('youtube_videos');
        });
    }
}
