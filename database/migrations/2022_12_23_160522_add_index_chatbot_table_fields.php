<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexChatbotTableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::statement('ALTER TABLE `chatbot_keyword_values` ADD INDEX(`chatbot_keyword_id`);');
		 DB::statement('ALTER TABLE `chatbot_keyword_value_types` ADD INDEX(`chatbot_keyword_value_id`);');
		 DB::statement('ALTER TABLE `chatbot_questions_reply` ADD INDEX(`store_website_id`);');
		 DB::statement('ALTER TABLE `chatbot_questions_reply` ADD INDEX(`chatbot_question_id`);');
		 DB::statement('ALTER TABLE `chatbot_question_examples` ADD INDEX(`chatbot_question_id`);');
		 DB::statement('ALTER TABLE `chat_bot_phrase_groups` ADD INDEX(`phrase_id`);');
		 DB::statement('ALTER TABLE `chat_bot_phrase_groups` ADD INDEX(`keyword_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`hubstuff_activity_user_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`chatbot_dialog_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`store_website_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`reply_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD INDEX(`chatbot_dialog_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD INDEX(`store_website_id`); ');
		 DB::statement('ALTER TABLE `chatbot_replies` ADD INDEX(`is_read`);');
		 DB::statement('ALTER TABLE `chat_message_phrases` ADD INDEX(`word_id`);');
		 DB::statement('ALTER TABLE `chat_message_phrases` ADD INDEX(`chat_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`test_suites_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`bug_id`);');
		 DB::statement('ALTER TABLE `chat_messages` CHANGE `bug_id` `bug_id` INT(11) UNSIGNED NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         DB::statement('ALTER TABLE `chatbot_keyword_values` ADD INDEX(`chatbot_keyword_id`);');
		 DB::statement('ALTER TABLE `chatbot_keyword_value_types` ADD INDEX(`chatbot_keyword_value_id`);');
		 DB::statement('ALTER TABLE `chatbot_questions_reply` ADD INDEX(`store_website_id`);');
		 DB::statement('ALTER TABLE `chatbot_questions_reply` ADD INDEX(`chatbot_question_id`);');
		 DB::statement('ALTER TABLE `chatbot_question_examples` ADD INDEX(`chatbot_question_id`);');
		 DB::statement('ALTER TABLE `chat_bot_phrase_groups` ADD INDEX(`phrase_id`);');
		 DB::statement('ALTER TABLE `chat_bot_phrase_groups` ADD INDEX(`keyword_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`hubstuff_activity_user_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`chatbot_dialog_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`store_website_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_error_logs` ADD INDEX(`reply_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD INDEX(`chatbot_dialog_id`);');
		 DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD INDEX(`store_website_id`); ');
		 DB::statement('ALTER TABLE `chatbot_replies` ADD INDEX(`is_read`);');
		 DB::statement('ALTER TABLE `chat_message_phrases` ADD INDEX(`word_id`);');
		 DB::statement('ALTER TABLE `chat_message_phrases` ADD INDEX(`chat_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`test_suites_id`);');
		 DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`bug_id`);');
		 DB::statement('ALTER TABLE `chat_messages` CHANGE `bug_id` `bug_id` INT(11) UNSIGNED NULL DEFAULT NULL;');
    }
}
