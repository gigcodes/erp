<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorServersHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('monitor_servers_history');

        Schema::create('monitor_servers_history', function (Blueprint $table) {
            $table->increments('servers_history_id');
            $table->unsignedInteger('server_id');
            $table->date('date');
            $table->float('latency_min', 9, 7);
            $table->float('latency_avg', 9, 7);
            $table->float('latency_max', 9, 7);
            $table->unsignedInteger('checks_total');
            $table->unsignedInteger('checks_failed');
            $table->unique(['server_id', 'date']);
            $table->timestamps();
        });

        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `monitor_servers_history` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_servers_history');
    }
}
