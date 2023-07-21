<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModuleDependencyToMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->text('magento_dependency')->nullable();
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
            $table->dropColumn("magento_dependency");
        });
    }
}
