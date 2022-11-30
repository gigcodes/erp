<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetUserChangeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_user_change_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('new_user_id')->nullable();
            $table->string('old_user_id')->nullable();
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
        Schema::dropIfExists('asset_user_change_histories');
    }
}
