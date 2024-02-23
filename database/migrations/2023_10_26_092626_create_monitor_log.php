<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('monitor_log');

        Schema::create('monitor_log', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('server_id');
            $table->enum('type', ['status', 'email', 'sms', 'pushover', 'telegram', 'jabber']);
            $table->text('message');
            $table->timestamp('datetime')->default(now());
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `monitor_log` ENGINE = MyISAM');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_log');
    }
}
