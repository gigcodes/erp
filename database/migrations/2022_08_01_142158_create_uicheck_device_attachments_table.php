<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUicheckDeviceAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uicheck_device_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ui_devices_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('device_no')->nullable();
            $table->integer('uicheck_id')->nullable();
            $table->longText('attachment')->nullable();
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
        Schema::dropIfExists('uicheck_device_attachments');
    }
}
