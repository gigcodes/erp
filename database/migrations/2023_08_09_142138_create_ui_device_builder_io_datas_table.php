<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUiDeviceBuilderIoDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('uicheck_id');
            $table->integer('ui_device_id');
            $table->string('title');
            $table->text('html');
            $table->bigInteger('builder_created_date')->nullable();
            $table->bigInteger('builder_last_updated')->nullable();
            $table->string('builder_created_by')->nullable();
            $table->string('builder_last_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ui_device_builder_io_datas');
    }
}
