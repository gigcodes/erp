<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('whatsapp_api_logs')) {
            Schema::create('whatsapp_api_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('logs_id')->index();
                $table->String('mobile');
                $table->String('status');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_api_logs');
    }
}
