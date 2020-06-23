<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDutyGroupCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duty_group_countries', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedBigInteger('duty_group_id');
            $table->foreign('duty_group_id')->references('id')->on('duty_groups');

            $table->unsignedBigInteger('country_duty_id');
            $table->foreign('country_duty_id')->references('id')->on('country_duties');

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
        Schema::dropIfExists('duty_group_countries');
    }
}
