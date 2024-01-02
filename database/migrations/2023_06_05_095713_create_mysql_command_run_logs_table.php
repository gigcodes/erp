<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMysqlCommandRunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mysql_command_run_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('website_ids')->nullable();
            $table->string('server_ip')->nullable();
            $table->text('command')->nullable();
            $table->text('response')->nullable();
            $table->string('job_id')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('mysql_command_run_logs');
    }
}
