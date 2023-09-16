<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoomApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_api_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('request_url');
            $table->string('type')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('request_data')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_api_logs');
    }
}
