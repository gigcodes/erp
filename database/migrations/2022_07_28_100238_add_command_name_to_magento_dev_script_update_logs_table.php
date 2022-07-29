<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommandNameToMagentoDevScriptUpdateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_dev_script_update_logs', function (Blueprint $table) {
            $table->string('command_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_dev_script_update_logs', function (Blueprint $table) {
            $table->dropColumn('command_name');
        });
    }
}
