<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlFieldsDatabaseLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('database_logs', function (Blueprint $table) {
            $table->bigInteger('time_taken')->nullable()->after('logmessage');
            $table->string('url')->nullable()->after('time_taken');
            $table->longText('sql_data')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('database_logs', function (Blueprint $table) {
            $table->dropColumn('time_taken');
            $table->dropColumn('url');
            $table->dropColumn('sql_data');
        });
    }
}
