<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoModuleCronJobHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_cron_job_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('magento_module_id');
            $table->string('cron_time', 200)->nullable();
            $table->string('frequency', 200)->nullable();
            $table->string('cpu_memory', 200)->nullable();
            $table->string('comments', 200)->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('magento_module_cron_job_histories');
    }
}
