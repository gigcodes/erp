<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('task_id');
            $table->dateTimeTz('starts_at');
            $table->string('tracked')->nullable();
            $table->integer('overall')->default(0);
            $table->integer('time_doctor_payment_account_id')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('paid')->default(0);
            $table->boolean('is_manual')->default(0);
            $table->text('user_notes');
            $table->string('project_id');
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
        Schema::dropIfExists('time_doctor_activities');
    }
}
