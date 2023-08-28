<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueDateToTimeDoctorAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_doctor_accounts', function (Blueprint $table) {
            $table->datetime('due_date')->nullable();
            $table->boolean('validate')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_doctor_accounts', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('validate');
        });
    }
}
