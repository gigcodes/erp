<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultDutyToSimplyDutyCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simply_duty_countries', function (Blueprint $table) {
            $table->double('default_duty', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simply_duty_countries', function (Blueprint $table) {
            $table->double('default_duty', 10, 2)->default(0);
        });
    }
}
