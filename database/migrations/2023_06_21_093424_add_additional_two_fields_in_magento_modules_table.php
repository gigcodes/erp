<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalTwoFieldsInMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->integer('dev_verified_by')->nullable()->after('developer_name');
            $table->integer('dev_verified_status_id')->nullable()->after('dev_verified_by');
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
            $table->dropColumn('dev_verified_by');
            $table->dropColumn('dev_verified_status_id');
        });
    }
}
