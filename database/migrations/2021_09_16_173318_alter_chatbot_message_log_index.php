<?php

use Illuminate\Database\Migrations\Migration;

class AlterChatbotMessageLogIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `chatbot_message_log_responses` ADD INDEX(`chatbot_message_log_id`);');
        \DB::statement('ALTER TABLE `chatbot_message_logs` ADD INDEX(`model_id`);');
        \DB::statement('ALTER TABLE `chatbot_message_logs` ADD INDEX(`chat_message_id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
