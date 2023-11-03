<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoCronListHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_cron_list_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('cron_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('store_website_id')->default(0);
            $table->string('server_ip', 255);
            $table->dateTime('last_execution_time');
            $table->longText('request_data');
            $table->longText('response_data');
            $table->string('job_id', 255);
            $table->boolean('status'); // 0 for success, 1 for failure
            $table->longText('working_directory');
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
        Schema::dropIfExists('magento_cron_list_histories');
    }
}
