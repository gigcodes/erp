<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScraperResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraper_results', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->date('date');
            $table->string('scraper_name');
            $table->integer('total_urls');
            $table->integer('existing_urls');
            $table->integer('new_urls');
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
        Schema::dropIfExists('scraper_results');
    }
}
