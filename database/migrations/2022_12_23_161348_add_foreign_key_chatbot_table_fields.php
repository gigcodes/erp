<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddForeignKeyChatbotTableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD FOREIGN KEY (`store_website_id`) REFERENCES `store_websites`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD FOREIGN KEY (`chatbot_dialog_id`) REFERENCES `chatbot_dialogs`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_keyword_values` ADD  FOREIGN KEY (`chatbot_keyword_id`) REFERENCES `chatbot_keywords`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_question_examples` ADD FOREIGN KEY (`chatbot_question_id`) REFERENCES `chatbot_questions`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chat_messages` ADD FOREIGN KEY (`bug_id`) REFERENCES `bug_trackers`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD FOREIGN KEY (`store_website_id`) REFERENCES `store_websites`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD FOREIGN KEY (`chatbot_dialog_id`) REFERENCES `chatbot_dialogs`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_keyword_values` ADD  FOREIGN KEY (`chatbot_keyword_id`) REFERENCES `chatbot_keywords`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chatbot_question_examples` ADD FOREIGN KEY (`chatbot_question_id`) REFERENCES `chatbot_questions`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
        DB::statement('ALTER TABLE `chat_messages` ADD FOREIGN KEY (`bug_id`) REFERENCES `bug_trackers`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
    }
}
