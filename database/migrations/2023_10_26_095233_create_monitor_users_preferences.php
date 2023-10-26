<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorUsersPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('monitor_users_preferences');
        
        Schema::create('monitor_users_preferences', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('key', 255);
            $table->string('value', 255);
            $table->primary(['user_id', 'key']);
            $table->timestamps();
        });

        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
  
        //DB::statement("ALTER TABLE `monitor_users_preferences` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('monitor_users_preferences');
    }
}

