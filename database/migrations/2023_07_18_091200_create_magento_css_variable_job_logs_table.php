<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoCssVariableJobLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_css_variable_id');
            $table->text('command');
            $table->text('message');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_css_variable_job_logs');
    }
}
