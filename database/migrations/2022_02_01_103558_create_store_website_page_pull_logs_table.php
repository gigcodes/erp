<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsitePagePullLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_page_pull_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_id')->nullable();
            $table->integer('page_id')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->string('url_key')->nullable();
            $table->string('title')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('content_heading')->nullable();
            $table->text('content')->nullable();
            $table->string('layout')->nullable();
            $table->string('response_type')->nullable();
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
        Schema::dropIfExists('store_website_page_pull_logs');
    }
}
