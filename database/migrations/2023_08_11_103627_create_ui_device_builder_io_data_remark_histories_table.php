<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUiDeviceBuilderIoDataRemarkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_device_builder_io_data_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('ui_device_builder_io_data_id');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('ui_device_builder_io_data_remark_histories');
    }
}
