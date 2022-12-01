<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableScraperAutoRestart extends Migration
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
            $table->integer('auto_restart')->default(0)->nullable()->after('run_gap');
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
            $table->dropField('auto_restart');
        });
    }
}
