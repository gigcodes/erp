<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigRefactorUserHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_refactor_user_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('config_refactor_id');
            $table->integer('old_user')->nullable();
            $table->integer('new_user')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_refactor_user_histories');
    }
}
