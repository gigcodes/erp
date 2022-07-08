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
            $table->float('free_inode_in', 6, 2);
            $table->float('space_utilization', 6, 2);
            $table->float('total_space', 6, 2);
            $table->float('used_space ', 6, 2);
            $table->float('available_memory', 3, 1);
            $table->float('available_memory_in', 6, 2);
            $table->float('cpu_idle_time', 6, 2);
            $table->float('cpu_utilization', 6, 2);
            $table->float('interrupts_per_second', 8, 4);
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
