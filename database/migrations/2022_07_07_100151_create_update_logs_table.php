<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('api_url')->nullable();
            $table->string('device')->nullable();
            $table->string('api_type')->nullable();
            $table->string('user_id')->nullable();
            $table->text('request_header')->nullable();
            $table->string('app_version')->nullable();
            $table->text('other')->nullable();
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
        Schema::dropIfExists('update_logs');
    }
}
