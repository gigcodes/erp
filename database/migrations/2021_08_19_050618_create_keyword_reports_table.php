<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeywordReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyword_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('keyword_overview_all_database');
            $table->text('keyword_overview_one_database');
            $table->text('batch_keyword_overview_one_database');
            $table->text('organic_results');
            $table->text('paid_results');
            $table->text('related_keyword');
            $table->text('keyword_ads_history');
            $table->text('broad_match_keywords');
            $table->text('phrase_questions');
            $table->text('keyword_difficulty');
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
        Schema::dropIfExists('keyword_reports');
    }
}
