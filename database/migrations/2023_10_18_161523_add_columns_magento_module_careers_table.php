<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsMagentoModuleCareersTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::table('magento_module_careers', function (Blueprint $table) {
            $table->string('title')->nullable();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('magento_module_careers', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
