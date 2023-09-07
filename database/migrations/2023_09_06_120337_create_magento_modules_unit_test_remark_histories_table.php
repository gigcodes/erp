<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModulesUnitTestRemarkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_modules_unit_test_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_module_id');
            $table->text('old_unit_test_remark')->nullable();
            $table->text('new_unit_test_remark')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_modules_unit_test_remark_histories');
    }
}
