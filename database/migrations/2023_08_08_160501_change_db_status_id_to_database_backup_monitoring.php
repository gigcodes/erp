<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDbStatusIdToDatabaseBackupMonitoring extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->integer('db_status_id')->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->dropColumn('db_status_id');
        });
    }
}