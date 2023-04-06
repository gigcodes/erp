<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYoutubeCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youtube_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('youtube_video_id');
            $table->string('video_id',128);
            $table->string('comment_id', 128);
            $table->string('title', 564)->nullable();
            $table->int('like_count', 11)->nullable();
            $table->int('dislike_count', 11)->nullable();
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
        Schema::table('youtube_comments', function (Blueprint $table) {
            Schema::dropIfExists('youtube_comments');
        });
    }
}
