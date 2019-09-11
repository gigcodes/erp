<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSuppliersAddIndexesForScraperName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::table('suppliers', function (Blueprint $table) {
            $table->index('scraper_name');
        });

        Schema::table('log_scraper', function (Blueprint $table) {
            $table->index('website');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function ($table) {
            $table->dropIndex('suppliers_scraper_name_index');
        });

        Schema::table('log_scraper', function (Blueprint $table) {
            $table->dropIndex('log_scraper_website_index');
        });
    }
}
