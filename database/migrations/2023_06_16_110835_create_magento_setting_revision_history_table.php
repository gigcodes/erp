<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoSettingRevisionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_setting_revision_history', function (Blueprint $table) {
            $table->id();
            $table->string('setting')->nullable();
            $table->dateTime('date')->nullable();
            $table->boolean('status')->nullable();
            $table->longText('log')->nullable();
            $table->longText('config_revision')->nullable();
            $table->boolean('active')->nullable();
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
        Schema::dropIfExists('magento_setting_revision_history');
    }
}
