<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapperPythonActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapper_python_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('website')->nullable();
            $table->string('action')->nullable();
            $table->string('device')->nullable();
            $table->string('url')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
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
        Schema::dropIfExists('scrapper_python_action_logs');
    }
}
