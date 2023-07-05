<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseBackupMonitoringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_backup_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('server_name', 50)->nullable();
            $table->string('instance', 50)->nullable();
            $table->string('database_name', 20)->nullable();
            $table->dateTime('date')->nullable();
            $table->boolean('status')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_backup_monitoring');
    }
}
