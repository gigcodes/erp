<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('project_id');
            $table->integer('issue_id');
            $table->string('title');
            $table->text('desc');
            $table->text('title_page');
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
        Schema::dropIfExists('site_issues');
    }
}
