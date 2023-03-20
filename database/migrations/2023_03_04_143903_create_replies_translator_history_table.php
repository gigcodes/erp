<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepliesTranslatorHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replies_translator_history', function (Blueprint $table) {
            $table->id();
            $table->integer('translate_replies_id')->nullable();
            $table->string('lang')->nullable();
            $table->string('translate_text')->nullable();
            $table->string('status')->nullable();
            $table->integer('updated_by_user_id')->nullable();
            $table->integer('approved_by_user_id')->nullable();
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
        Schema::dropIfExists('replies_translator_history');
    }
}
