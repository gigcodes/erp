<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlowActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('path_id');
            $table->integer('type_id');
            $table->integer('rank');
            $table->integer('time_delay');
            $table->string('time_delay_type');
            $table->string('deleted');
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
        Schema::dropIfExists('flow_actions');
    }
}
