<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorUsersServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('monitor_users_servers');
        
        Schema::create('monitor_users_servers', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('server_id');
            $table->primary(['user_id', 'server_id']);
        });

        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `monitor_users_servers` ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_users_servers');
    }
}
