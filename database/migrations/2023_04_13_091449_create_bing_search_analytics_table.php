<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBingSearchAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bing_search_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->float('clicks')->nullable();
            $table->float('impression')->nullable();
            $table->float('ctr')->nullable();
            $table->float('position')->nullable();
            $table->string('page', 191)->nullable();
            $table->string('query', 191)->nullable();
            $table->date('date')->nullable();
            $table->float('crawl_requests')->nullable();
            $table->float('crawl_errors')->nullable();
            $table->float('index_pages')->nullable();
            $table->string('crawl_information')->nullable();
            $table->string('keywords')->nullable();
            $table->string('pages')->nullable();
            $table->foreign('site_id')->references('id')->on('bing_sites')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('bing_search_analytics');
    }
}
