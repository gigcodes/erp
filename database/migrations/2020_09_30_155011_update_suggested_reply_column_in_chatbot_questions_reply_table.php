<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSuggestedReplyColumnInChatbotQuestionsReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatbot_questions_reply', function (Blueprint $table) {
            $table->text('suggested_reply')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_questions_reply', function (Blueprint $table) {
            //
        });
    }
}
