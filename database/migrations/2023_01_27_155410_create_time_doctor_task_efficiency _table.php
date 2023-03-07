<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorTaskEfficiencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_task_efficiencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('admin_input')->nullable();
            $table->string('user_input')->nullable();
            $table->date('date');
            $table->integer('time');
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
        Schema::dropIfExists('time_doctor_task_efficiencies');
    }
}
