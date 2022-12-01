<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainLandingPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_landing_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->text('database');
            $table->text('target_url');
            $table->text('first_seen');
            $table->text('last_seen');
            $table->text('times_seen');
            $table->text('ads_count');
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
        Schema::dropIfExists('domain_landing_pages');
    }
}
