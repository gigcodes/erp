<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorCommandLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_command_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('day');
            $table->string('weekly');
            $table->string('biweekly');
            $table->string('fornightly');
            $table->string('monthly');
            $table->string('userCount');
            $table->text('messages');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_doctor_command_logs');
    }
}