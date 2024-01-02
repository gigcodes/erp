<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobIdFieldInMagentoCommandRunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_command_run_logs', function (Blueprint $table) {
            $table->string('job_id')->nullable()->after('response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_command_run_logs', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });
    }
}
