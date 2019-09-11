<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links_to_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('link')->nullable();
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->date('date_scrapped')->nullable();
            $table->dateTime('date_posted')->nullable();
            $table->dateTime('date_next_post')->nullable();
            $table->string('article')->nullable();
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
        Schema::dropIfExists('links_to_posts');
    }
}
