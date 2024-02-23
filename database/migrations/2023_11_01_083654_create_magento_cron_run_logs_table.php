<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoCronRunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_cron_run_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('command_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('website_ids')->nullable();
            $table->string('server_ip')->nullable();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->string('job_id')->nullable();
            $table->string('status')->nullable();
            $table->text('command_name')->nullable();
            $table->text('working_directory')->nullable();
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
        Schema::dropIfExists('magento_cron_run_logs');
    }
}
