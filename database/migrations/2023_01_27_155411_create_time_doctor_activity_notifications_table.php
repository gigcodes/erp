<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorActivityNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_activity_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('time_doctor_user_id')->nullable();
            $table->string('total_track')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->float('min_percentage')->default('0.00');
            $table->float('actual_percentage')->default('0.00');
            $table->text('reason')->nullable();
            $table->integer('status');
            $table->string('client_remarks')->nullable();
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
        Schema::dropIfExists('time_doctor_activity_notifications');
    }
}
