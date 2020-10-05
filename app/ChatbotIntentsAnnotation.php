<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotIntentsAnnotation extends Model
{
    protected $fillable = [
        'question_example_id', 'chatbot_keyword_id', 'start_char_range','end_char_range','chatbot_value_id'
    ];

    public function questionExample()
    {
    	return $this->hasOne("\App\ChatbotQuestionExample","id","question_example_id");
    }

    // public function chatbotKeyword()
    // {
    // 	return $this->hasOne("\App\ChatbotKeyword","id","chatbot_keyword_id");
    // }

    public function chatbotQuestion()
    {
    	return $this->hasOne("\App\ChatbotQuestion","id","chatbot_keyword_id");
    }

    
}
