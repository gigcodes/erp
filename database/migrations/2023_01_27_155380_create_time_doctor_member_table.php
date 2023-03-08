<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time_doctor_user_id')->nullable();
            $table->integer('time_doctor_account_id');
            $table->string('email')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('pay_rate', 8, 2);
            $table->float('bill_rate', 8, 2);
            $table->string('currency')->nullable();
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
        Schema::dropIfExists('time_doctor_members');
    }
}
