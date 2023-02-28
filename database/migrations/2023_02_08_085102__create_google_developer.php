<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDeveloper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_developer_reporting', function (Blueprint $table) {
            $table->increments('google_dev_id');
          
            $table->string('name');
            $table->string('aggregation_period');
            $table->string('latestEndTime');
            $table->string('timezone');
            $table->string('report');
            
            $table->timestamp('inserted_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_developer_reporting');
    }
}
