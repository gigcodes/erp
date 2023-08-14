<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStatusInDatabaseBackupMonitoring extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->renameColumn('status', 'db_status_id');
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
            $table->renameColumn('db_status_id', 'status');
        });
    }
}
