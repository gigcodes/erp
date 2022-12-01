<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainSearchKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_search_keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->enum('subtype', ['organic', 'paid']);
            $table->text('keyword');
            $table->integer('position');
            $table->integer('previous_position');
            $table->integer('position_difference');
            $table->integer('search_volume');
            $table->integer('cpc');
            $table->text('url');
            $table->integer('traffic');
            $table->integer('traffic_percentage');
            $table->integer('traffic_cost');
            $table->integer('competition');
            $table->integer('number_of_results');
            $table->text('trends');
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
        Schema::dropIfExists('domain_search_keywords');
    }
}
