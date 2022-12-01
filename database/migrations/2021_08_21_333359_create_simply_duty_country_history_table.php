<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimplyDutyCountryHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simply_duty_country_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('simply_duty_countries_id')->nullable();
            $table->integer('old_segment')->nullable();
            $table->integer('new_segment')->nullable();
            $table->integer('old_duty')->nullable();
            $table->integer('new_duty')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('simply_duty_country_histories');
    }
}
