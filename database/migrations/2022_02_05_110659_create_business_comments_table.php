<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_comments', function (Blueprint $table) {
            $table->string('comment_id')->primary();
            $table->string('post_id');
            $table->foreign('post_id')->references('post_id')->on('business_posts')->onDelete('cascade');
            $table->boolean('is_admin_comment')->default(0);
            $table->unsignedBigInteger('social_contact_id');
            $table->text('message')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_parent')->default(0);
            $table->string('parent_comment_id')->nullable();
            $table->string('verb', 20);
            $table->datetime('time');
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
        Schema::dropIfExists('business_comments');
    }
}
