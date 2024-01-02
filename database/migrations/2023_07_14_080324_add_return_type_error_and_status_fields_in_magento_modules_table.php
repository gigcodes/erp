<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnTypeErrorAndStatusFieldsInMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->string('return_type_error')->nullable();
            $table->string('return_type_error_status')->nullable();
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
            $table->dropColumn('return_type_error');
            $table->dropColumn('return_type_error_status');
        });
    }
}
