<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewcolumnToDatabaseBackupMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->boolean('is_resolved')->default(0)->nullable();
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
            $table->dropColumn('is_resolved');
        });
    }
}
