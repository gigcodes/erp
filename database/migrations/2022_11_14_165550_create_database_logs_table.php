<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('log_message')->nullable();
            $table->bigInteger('time_taken')->nullable();
            $table->string('url')->nullable();
            $table->longText('sql_data')->nullable();
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
        Schema::dropIfExists('database_logs');
    }
}
