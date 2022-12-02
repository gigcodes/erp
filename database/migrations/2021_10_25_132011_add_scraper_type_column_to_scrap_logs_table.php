<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScraperTypeColumnToScrapLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scrap_logs', function (Blueprint $table) {
            $table->string('scrap_type')->after('scraper_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scrap_logs', function (Blueprint $table) {
            $table->dropColumn('scrap_type');
        });
    }
}
