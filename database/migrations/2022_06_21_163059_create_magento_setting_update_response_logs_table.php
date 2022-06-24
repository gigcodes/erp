<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoSettingUpdateResponseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_setting_update_response_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_id')->nullable();
            $table->integer('magento_setting_id')->nullable();
            $table->longText('response')->nullable();
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
        Schema::dropIfExists('magento_setting_update_response_logs');
    }
}
