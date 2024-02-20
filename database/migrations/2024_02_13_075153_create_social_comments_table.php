<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment_ref_id');
            $table->string('commented_by_id');
            $table->string('commented_by_user');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('config_id');
            $table->string('message');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('social_comments');
    }
}
