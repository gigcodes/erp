<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoModuleLocationsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_module_locations_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_module_id');
            $table->integer('old_location_id')->nullable();
            $table->integer('new_location_id')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_module_locations_histories');
    }
}
