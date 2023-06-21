<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorServersUptimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_servers_uptimes', function (Blueprint $table) {
            $table->id();
            $table->integer('monitor_server_id')->unsigned();
            $table->dateTime('date');
            $table->unsignedTinyInteger('status');
            $table->float('latency', 9, 7)->nullable();
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
        Schema::dropIfExists('monitor_servers_uptimes');
    }
}
