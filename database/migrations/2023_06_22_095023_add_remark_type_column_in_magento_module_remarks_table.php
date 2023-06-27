<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarkTypeColumnInMagentoModuleRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->string('type')->after('remark')->default('general');
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
            $table->dropColumn('type');
        });
    }
}
