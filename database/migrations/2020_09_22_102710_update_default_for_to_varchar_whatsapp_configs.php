<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultForToVarcharWhatsappConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whatsapp_configs', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `whatsapp_configs` CHANGE `default_for` `default_for` VARCHAR(255) NULL DEFAULT NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whatsapp_configs', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `whatsapp_configs` CHANGE `default_for` `default_for` INT(5) NULL DEFAULT NULL');
        });
    }
}
