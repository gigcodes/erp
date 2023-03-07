<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time_doctor_project_id');
            $table->string('time_doctor_account_id');
            $table->string('time_doctor_company_id')->nullable();
            $table->string('time_doctor_project_name');
            $table->text('time_doctor_project_description')->nullable();
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
        Schema::dropIfExists('time_doctor_projects');
    }
}
