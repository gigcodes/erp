<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotKeywordValueTypes extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'type', 'chatbot_keyword_value_id',
    ];
}
