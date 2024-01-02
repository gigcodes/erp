<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddM2ErrorColumnsInMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->integer('m2_error_status_id')->nullable();
            $table->integer('m2_error_assignee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('m2_error_status_id');
            $table->dropColumn('m2_error_assignee');
        });
    }
}
