<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExecutionLogFieldInMagentoCronDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_cron_datas', function (Blueprint $table) {
            $table->longText("execution_log")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_cron_datas', function (Blueprint $table) {
            $table->dropColumn("execution_log");
        });
    }
}
