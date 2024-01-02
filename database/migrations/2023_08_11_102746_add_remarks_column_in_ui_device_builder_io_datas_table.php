<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarksColumnInUiDeviceBuilderIoDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
}
