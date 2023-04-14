<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_logs', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->text('payload')->nullable();
            $table->text('response')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('response_code');
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
        Schema::dropIfExists('time_doctor_logs');
    }
}
