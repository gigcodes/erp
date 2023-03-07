<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorCommandLogsMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_command_log_messages', function (Blueprint $table) {
            $table->increments('id');
            /*$table->integer('time_doctor_command_log_id')->unsigned()->index()->foreign()->references('id')->on('time_doctor_command_logs')->onDelete('cascade');*/
            $table->integer('time_doctor_command_log_id')->unsigned();
            $table->foreign('time_doctor_command_log_id', 'id')->references('id')->on('time_doctor_command_logs')->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('time_doctor_command_log_messages');
    }
}
