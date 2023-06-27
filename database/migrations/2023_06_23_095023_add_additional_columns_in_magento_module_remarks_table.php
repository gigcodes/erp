<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsInMagentoModuleRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->text('frontend_issues')->after('type')->nullable();
            $table->text('backend_issues')->after('frontend_issues')->nullable();
            $table->text('security_issues')->after('backend_issues')->nullable();
            $table->text('performance_issues')->after('security_issues')->nullable();
            $table->text('best_practices')->after('performance_issues')->nullable();
            $table->text('conclusion')->after('best_practices')->nullable();
            $table->text('other')->after('conclusion')->nullable();
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
            $table->dropColumn('frontend_issues');
            $table->dropColumn('backend_issues');
            $table->dropColumn('security_issues');
            $table->dropColumn('performance_issues');
            $table->dropColumn('best_practices');
            $table->dropColumn('conclusion');
            $table->dropColumn('other');
        });
    }
}
