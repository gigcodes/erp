<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUicheckLangAttchmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uicheck_lang_attchments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ui_languages_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('languages_id')->nullable();
            $table->integer('uicheck_id')->nullable();
            $table->longText('attachment')->nullable();
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
        Schema::dropIfExists('uicheck_lang_attchments');
    }
}
