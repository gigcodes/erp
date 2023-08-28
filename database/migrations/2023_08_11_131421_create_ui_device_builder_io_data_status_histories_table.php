<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUiDeviceBuilderIoDataStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_device_builder_io_data_status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('ui_device_builder_io_data_id');
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ui_device_builder_io_data_status_histories');
    }
}
