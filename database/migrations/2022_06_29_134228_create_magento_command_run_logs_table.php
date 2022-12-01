<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoCommandRunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_command_run_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('command_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('website_ids')->nullable();
            $table->string('command_name')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('command_type')->nullable();
            $table->string('response')->nullable();
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
        Schema::dropIfExists('magento_command_run_logs');
    }
}
