<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisplayAdvertisingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('display_advertising_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('publisher_display_ads');
            $table->text('advertisers');
            $table->text('publishers');
            $table->text('advertiser_display_ads');
            $table->text('landing_pages');
            $table->text('advertiser_display_ads_on_a_publishers_website');
            $table->text('advertisers_rank');
            $table->text('publishers_rank');
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
        Schema::dropIfExists('display_advertising_reports');
    }
}
