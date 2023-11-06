<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorLogUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('monitor_log_users');
        
        Schema::create('monitor_log_users', function (Blueprint $table) {
            $table->unsignedInteger('log_id');
            $table->unsignedInteger('user_id');
            $table->primary(['log_id', 'user_id']);
            $table->timestamps();
        });

        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `monitor_log_users` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_log_users');
    }
}
