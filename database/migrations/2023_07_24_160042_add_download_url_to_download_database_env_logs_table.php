<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDownloadUrlToDownloadDatabaseEnvLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('download_database_env_logs', function (Blueprint $table) {
            $table->text('download_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('download_database_env_logs', function (Blueprint $table) {
            $table->dropColumn('download_url')->nullable();
        });
    }
}
