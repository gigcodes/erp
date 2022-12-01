<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_posts', function (Blueprint $table) {
            $table->string('post_id')->primary();
            $table->unsignedInteger('social_config_id');
            $table->text('message')->nullable();
            $table->string('item', 20);
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
        Schema::dropIfExists('business_posts');
    }
}
