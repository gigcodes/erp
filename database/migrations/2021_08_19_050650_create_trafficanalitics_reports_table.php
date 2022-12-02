<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrafficanaliticsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trafficanalitics_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('traffic_summary');
            $table->text('traffic_sources');
            $table->text('traffic_destinations');
            $table->text('geo_distribution');
            $table->text('subdomains');
            $table->text('top_pages');
            $table->text('domain_rankings');
            $table->text('audience_insights');
            $table->text('data_accuracy');
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
        Schema::dropIfExists('trafficanalitics_reports');
    }
}
