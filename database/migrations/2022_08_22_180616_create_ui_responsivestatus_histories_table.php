<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUiResponsivestatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_responsivestatus_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ui_device_id')->nullable();
            $table->integer('uicheck_id')->nullable();
            $table->integer('device_no')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('status')->nullable();
            $table->string('old_status')->nullable();
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
        Schema::dropIfExists('ui_responsivestatus_histories');
    }
}
