<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetManamentupdateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_manamentupdate_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assetmenament_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('user_name')->nullable();
            $table->text('password')->nullable();
            $table->text('ip')->nullable();
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
        Schema::dropIfExists('asset_manamentupdate_logs');
    }
}
