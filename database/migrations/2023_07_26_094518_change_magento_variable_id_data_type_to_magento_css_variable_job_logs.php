<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMagentoVariableIdDataTypeToMagentoCssVariableJobLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->text('magento_css_variable_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->Integer('magento_css_variable_id')->change();
        });
    }
}
