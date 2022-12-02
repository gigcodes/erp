<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSEOAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain_authority')->nullable();
            $table->string('linking_authority')->nullable();
            $table->string('inbound_links')->nullable();
            $table->string('ranking_keywords')->nullable();
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
        Schema::dropIfExists('seo_analytics');
    }
}
