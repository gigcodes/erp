<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_audit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->integer('store_website_id');
            $table->string('name');
            $table->text('url');
            $table->text('status');
            $table->integer('errors');
            $table->integer('warnings');
            $table->integer('notices');
            $table->integer('broken');
            $table->integer('blocked');
            $table->integer('redirected');
            $table->integer('healthy');
            $table->integer('haveIssues');
            $table->integer('haveIssuesDelta');
            $table->text('defects');
            $table->text('markups');
            $table->text('depths');
            $table->tinyInteger('crawlSubdomains');
            $table->tinyInteger('respectCrawlDelay');
            $table->integer('canonical');
            $table->integer('user_agent_type');
            $table->text('last_audit');
            $table->integer('last_failed_audit');
            $table->integer('next_audit');
            $table->integer('running_pages_crawled');
            $table->integer('running_pages_limit');
            $table->integer('pages_crawled');
            $table->integer('pages_limit');
            $table->integer('total_checks');
            $table->integer('errors_delta');
            $table->integer('warnings_delta');
            $table->integer('notices_delta');
            $table->text('mask_allow');
            $table->text('mask_disallow');
            $table->text('removedParameters');
            $table->text('excluded_checks');
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
        Schema::dropIfExists('site_audit');
    }
}
