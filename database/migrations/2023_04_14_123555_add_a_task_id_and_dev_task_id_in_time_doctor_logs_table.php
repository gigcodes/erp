<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddATaskIdAndDevTaskIdInTimeDoctorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_doctor_logs', function (Blueprint $table) {
            $table->unsignedBigInteger("dev_task_id")->nullable();
            $table->unsignedBigInteger("task_id")->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_doctor_logs', function (Blueprint $table) {
            $table->dropColumn("dev_task_id");
            $table->dropColumn("task_id");
        });
    }
}
