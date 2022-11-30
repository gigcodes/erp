<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableScraperAddFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('scrapers', function (Blueprint $table) {
            $table->integer('flag')->default(0)->nullable()->after('auto_restart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('scrapers', function (Blueprint $table) {
            $table->dropField('flag');
        });
    }
}
