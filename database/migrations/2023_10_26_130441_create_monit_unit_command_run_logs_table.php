<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitUnitCommandRunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monit_unit_command_run_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->default(0);
            $table->string('xmlid')->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
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
        Schema::dropIfExists('monit_unit_command_run_logs');
    }
}
