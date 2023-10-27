<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('monitor_config');
        
        Schema::create('monitor_config', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->string('value', 255);
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
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
        Schema::dropIfExists('monitor_config');
    }
}
