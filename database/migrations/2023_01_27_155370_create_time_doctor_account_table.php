<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_accounts', function (Blueprint $table) {
            $table->increments('id');
			$table->string('time_doctor_email');
			$table->string('time_doctor_password');
            $table->string('project_id')->nullable();
            $table->string('organization_id')->nullable();
			$table->string('auth_token');
            $table->string('company_id')->nullable();
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
        Schema::dropIfExists('time_doctor_accounts');
    }
}
