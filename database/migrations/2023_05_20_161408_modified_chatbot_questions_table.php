<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifiedChatbotQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->integer('google_account_id')->nullable()->default(0)->after('watson_status');
            $table->string('google_status')->nullable();
            $table->string('google_response_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->dropColumn('google_account_id');
            $table->dropColumn('google_status');
            $table->dropColumn('google_response_id');
        });
    }
}
