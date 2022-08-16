<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUiDeviceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_device_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ui_devices_id')->nullable();
            $table->integer('uicheck_id')->nullable();
            $table->string('device_no')->nullable();
            $table->string('message')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('ui_device_histories');
    }
}
