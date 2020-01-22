<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotKeywordValue extends Model
{
	public $timestamps = false;
    protected $fillable = [
        'value', 'chatbot_keyword_id','types'
    ];

    public function chatbotKeywordValueTypes() {
        return $this->hasMany("App\ChatbotKeywordValueTypes", "chatbot_keyword_value_id", "id");
    }
}
