<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoCronListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('magento_cron_lists');

        Schema::create('magento_cron_lists', function (Blueprint $table) {
            $table->id();
            $table->longText('cron_name', 255);
            $table->dateTime('last_execution_time');
            $table->longText('last_message');
            $table->boolean('cron_status'); // 0 for success, 1 for failure
            $table->string('frequency', 20);
            $table->softDeletes();
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
        Schema::dropIfExists('magento_cron_lists');
    }
}
