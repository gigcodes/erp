<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostItemsTable extends Migration
{
    public function up()
    {
        Schema::create('host_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('host_id');
            // $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->integer('item_id');
            $table->integer('hostid');
            $table->double('free_inode_in', 6, 2);
            $table->double('space_utilization', 6, 2);
            $table->bigInteger('total_space');
            $table->bigInteger('used_space');
            $table->bigInteger('available_memory');
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
