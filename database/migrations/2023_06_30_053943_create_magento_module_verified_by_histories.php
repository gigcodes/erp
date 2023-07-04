<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoModuleVerifiedByHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_verified_by_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_module_id');
            $table->integer('old_verified_by_id')->nullable();
            $table->integer('new_verified_by_id')->nullable();
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
        Schema::dropIfExists('magento_module_verified_by_histories');
    }
}
