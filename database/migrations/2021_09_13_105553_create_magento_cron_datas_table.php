<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoCronDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_cron_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('cron_id');
            $table->string('job_code');
            $table->text('cron_message')->nullable();
            $table->string('website');
            $table->string('cronstatus');
            $table->timestamp('cron_created_at')->nullable();
            $table->timestamp('cron_scheduled_at')->nullable();
            $table->timestamp('cron_executed_at')->nullable();
            $table->timestamp('cron_finished_at')->nullable();
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
        Schema::dropIfExists('magento_cron_datas');
    }
}
