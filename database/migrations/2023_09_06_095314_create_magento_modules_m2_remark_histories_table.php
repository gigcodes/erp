<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoModulesM2RemarkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_modules_m2_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_module_id');
            $table->text('old_m2_error_remark')->nullable();
            $table->text('new_m2_error_remark')->nullable();
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
        Schema::dropIfExists('magento_modules_m2_remark_histories');
    }
}
