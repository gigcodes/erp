<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronJobErroLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_job_erro_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('signature')->nullable();
            $table->string('priority')->nullable();
            $table->longText('error')->nullable();
            $table->integer('error_count')->nullable();
            $table->string('status')->nullable();
            $table->string('module')->nullable();
            $table->text('subject')->nullable();
            $table->longText('assigned_to')->nullable();
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
        Schema::dropIfExists('cron_job_erro_logs');
    }
}
