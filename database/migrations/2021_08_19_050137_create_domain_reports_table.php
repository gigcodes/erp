<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->string('database');
            $table->text('domain_organic_search_keywords');
            $table->text('domain_paid_search_keywords');
            $table->text('ads_copies');
            $table->text('competitors_in_organic_search');
            $table->text('competitors_in_paid_search');
            $table->text('domain_ad_history');
            $table->text('domain_vs_domain');
            $table->text('domain_pla_search_keywords');
            $table->text('pla_copies');
            $table->text('pla_competitors');
            $table->text('domain_organic_pages');
            $table->text('domain_organic_subdomains');
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
        Schema::dropIfExists('domain_reports');
    }
}
