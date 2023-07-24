<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCsvFilePathColumnInMagentoCssVariableJobLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->string('csv_file_path')->nullable();
            $table->integer('magento_css_variable_id')->nullable()->change();
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
            $table->dropColumn('csv_file_path');
        });
    }
}
