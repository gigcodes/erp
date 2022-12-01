<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('scope', ['default', 'websites', 'stores'])->default('default')->index();
            $table->unsignedBigInteger('scope_id')->index();
            $table->string('name', 192)->index();
            $table->string('path', 192)->index();
            $table->text('value')->nullable();
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
        Schema::dropIfExists('magento_settings');
    }
}
