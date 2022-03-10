<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDirectionFieldToTwilioCallStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_call_statistics', function (Blueprint $table) {
            $table->tinyInteger('direction')->after('call_costing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_call_statistics', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
}
