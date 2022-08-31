<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUiTranslatorStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ui_translator_status_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("ui_language_id")->nullable();
            $table->integer("language_id")->nullable();
            $table->integer("uicheck_id")->nullable();
            $table->integer("user_id")->nullable();
            $table->string("status")->nullable();
            $table->string("old_status")->nullable();
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
        Schema::dropIfExists('ui_translator_status_histories');
    }
}
