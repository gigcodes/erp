<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestionExample extends Model
{
	public $timestamps =  false;
    protected $fillable = [
        'question', 'chatbot_question_id',
    ];
}
