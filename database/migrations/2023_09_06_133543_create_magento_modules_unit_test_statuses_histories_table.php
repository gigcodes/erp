<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModulesUnitTestStatusesHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_modules_unit_test_statuses_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_module_id');
            $table->integer('old_unit_test_status_id')->nullable();
            $table->integer('new_unit_test_status_id')->nullable();
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
        Schema::dropIfExists('magento_modules_unit_test_statuses_histories');
    }
}
