<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAnalyticDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_analytic_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_analytics_id')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('iso_code')->nullable();
            $table->string('user_type')->nullable();
            $table->string('avg_time_page')->nullable();
            $table->string('page')->nullable();
            $table->string('page_view')->nullable();
            $table->string('unique_page_views')->nullable();
            $table->string('exit_rate')->nullable();
            $table->string('entrances')->nullable();
            $table->string('entrance_rate')->nullable();
            $table->string('session')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
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
        Schema::dropIfExists('google_analytic_datas');
    }
}
