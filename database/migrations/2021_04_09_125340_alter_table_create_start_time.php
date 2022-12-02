<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCreateStartTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('scraper_server_status_histories', function (Blueprint $table) {
            $table->datetime('start_time')->nullable()->after('server_id');
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
        Schema::table('scraper_server_status_histories', function (Blueprint $table) {
            $table->dropField('start_time');
        });
    }
}
