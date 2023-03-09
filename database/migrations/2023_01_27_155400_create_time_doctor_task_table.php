<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time_doctor_task_id');
            $table->string('project_id');
            $table->string('time_doctor_project_id');
            $table->text('summery');
            $table->text('description')->nullable();
            $table->string('time_doctor_company_id')->nullable();
            $table->string('time_doctor_account_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('time_doctor_tasks');
    }
}
