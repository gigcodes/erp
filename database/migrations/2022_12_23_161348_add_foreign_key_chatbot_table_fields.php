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
		
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("49", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("50", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("63", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("68", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("83", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("108", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("114", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("115", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("118", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('INSERT INTO `chatbot_questions` (`id`, `value`, `suggested_reply`, `category_id`, `workspace_id`, `created_at`, `updated_at`, `keyword_or_question`, `sending_time`, `repeat`, `is_active`, `erp_or_watson`, `auto_approve`, `chat_message_id`, `task_category_id`, `assigned_to`, `task_description`, `task_type`, `repository_id`, `module_id`, `dynamic_reply`, `watson_account_id`, `watson_status`) VALUES ("119", "Test", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, "0", NULL, NULL, NULL, NULL, NULL, NULL, NULL, "0", NULL, NULL);');
		
		DB::statement('ALTER TABLE `chatbot_question_examples` ADD FOREIGN KEY (`chatbot_question_id`) REFERENCES `chatbot_questions`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
		
		
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        
    }
}
