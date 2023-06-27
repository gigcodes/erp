<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModuleVerifiedStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_verified_status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_module_id');
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_module_verified_status_histories');
    }
}
