<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSuppliersTableAddScraperColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
             $table->dateTime('scraper_start_time')->nullable()->default('0000-00-00 00:00:00');
             $table->text('scraper_logic', 65535)->nullable();
             $table->integer('scraper_madeby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('scraper_start_time');
            $table->dropColumn('scraper_logic');
            $table->dropColumn('scraper_madeby');
        });
    }
}
