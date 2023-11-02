<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoMediaSyncsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_media_syncs', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by');
            $table->integer('source_store_website_id');
            $table->integer('dest_store_website_id');
            $table->text('source_server_ip')->nullable();
            $table->text('source_server_dir')->nullable();
            $table->text('dest_server_ip')->nullable();
            $table->text('dest_server_dir')->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
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
        Schema::dropIfExists('magento_media_syncs');
    }
}
