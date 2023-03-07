<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->string('lead_time_doctor_task_id')->after('lead_hubstaff_task_id');
            $table->string('team_lead_time_doctor_task_id')->after('team_lead_hubstaff_task_id');
            $table->string('tester_time_doctor_task_id')->after('team_lead_time_doctor_task_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropColumn('lead_time_doctor_task_id')->after('lead_hubstaff_task_id');
            $table->dropColumn('team_lead_time_doctor_task_id')->after('team_lead_hubstaff_task_id');
            $table->dropColumn('tester_time_doctor_task_id')->after('team_lead_time_doctor_task_id');
        });
    }
};
