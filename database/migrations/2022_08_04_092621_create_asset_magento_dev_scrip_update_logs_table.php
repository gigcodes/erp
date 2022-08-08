<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMagentoDevScripUpdateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_magento_dev_scrip_update_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_manager_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('ip')->nullable();
            $table->longText('response')->nullable();
            $table->string('site_folder')->nullable();
            $table->string('command_name')->nullable();
            $table->string('error')->nullable();
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
        Schema::dropIfExists('asset_magento_dev_scrip_update_logs');
    }
}
