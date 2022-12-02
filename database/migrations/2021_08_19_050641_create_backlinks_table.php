<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBacklinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backlinks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('backlinks_overview');
            $table->text('backlinks');
            $table->text('tld_distribution');
            $table->text('anchors');
            $table->text('indexed_pages');
            $table->text('competitors');
            $table->text('comparison_by_referring_domains');
            $table->text('batch_comparison');
            $table->text('authority_score_profile');
            $table->text('categories_profile');
            $table->text('categories');
            $table->text('historical_data');
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
        Schema::dropIfExists('backlinks');
    }
}
