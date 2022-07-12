<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('host_id');
            $table->foreign('host_id')->references('hostid')->on('hosts')->onDelete('cascade');
            $table->integer('item_id','55');
            $table->integer('hostid','55');
            $table->double('free_inode_in', 6, 2);
            $table->double('space_utilization', 6, 2);
            $table->bigIncrements('total_space', 55);
            $table->bigIncrements('used_space', 55);
            $table->bigIncrements('available_memory',55);
            $table->double('available_memory_in', 6, 2);
            $table->double('cpu_idle_time', 6, 2);
            $table->double('cpu_utilization', 6, 2);
            $table->double('interrupts_per_second', 8, 4);
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
        Schema::dropIfExists('host_items');
    }
}
