<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiIssuesColumnInMagentoModuleRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->text('api_issues')->after('performance_issues')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->dropColumn('api_issues');
        });
    }
}
