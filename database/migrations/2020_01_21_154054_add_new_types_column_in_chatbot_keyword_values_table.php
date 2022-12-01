<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTypesColumnInChatbotKeywordValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatbot_keyword_values', function (Blueprint $table) {
            $table->enum('types', ['synonyms', 'patterns']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_keyword_values', function (Blueprint $table) {
            $table->dropColumn('types');
        });
    }
}
