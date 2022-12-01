<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramPostsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_posts_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instagram_post_id');
            $table->string('comment_id');
            $table->string('name');
            $table->string('username');
            $table->string('user_id');
            $table->longText('comment');
            $table->text('profile_pic_url');
            $table->dateTime('posted_at');
            $table->text('metadata')->nullable();
            $table->integer('people_id')->nullable();
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
        Schema::dropIfExists('instagram_posts_comments');
    }
}
