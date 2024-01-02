<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDependencyComposerInMagentoModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->text('dependency')->nullable();
            $table->text('composer')->nullable();
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
            $table->dropColumn('dependency');
            $table->dropColumn('composer');
        });
    }
}
