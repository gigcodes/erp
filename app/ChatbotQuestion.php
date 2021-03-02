<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestion extends Model
{
    protected $fillable = [
        'value', 'workspace_id', 'created_at', 'updated_at', 'keyword_or_question', 'category_id',
        'sending_time','repeat','is_active','erp_or_watson','suggested_reply','auto_approve','chat_message_id','task_category_id','assigned_to','task_description','task_type','repository_id','module_id','dynamic_reply','watson_account_id'
    ];

    public function chatbotQuestionExamples()
    {
    	return $this->hasMany("App\ChatbotQuestionExample","chatbot_question_id","id");
    }
    public function chatbotErrorLogs()
    {
    	return $this->hasMany("App\ChatbotErrorLog","chatbot_question_id","id");
    }
    
    public function chatbotKeywordValues()
    {
        return $this->hasMany("App\ChatbotKeywordValue", "chatbot_keyword_id", "id");
    }

    public function chatbotQuestionReplies()
    {
    	return $this->hasMany("App\ChatbotQuestionReply","chatbot_question_id","id");
    }
}
