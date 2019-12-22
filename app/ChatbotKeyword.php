<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotKeyword extends Model
{
    protected $fillable = [
        'keyword', 'workspace_id',
    ];

    public function chatbotKeywordValues()
    {
        return $this->hasMany("App\ChatbotKeywordValue", "chatbot_keyword_id", "id");
    }
}
