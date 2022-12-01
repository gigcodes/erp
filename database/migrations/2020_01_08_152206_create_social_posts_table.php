<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->text('caption')->nullable();
            $table->text('post_body')->nullable();
            $table->integer('post_by');
            $table->timestamp('posted_on')->nullable();
            $table->string('ref_post_id')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('social_posts');
    }
}
