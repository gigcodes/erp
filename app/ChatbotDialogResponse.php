<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialogResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'response_type', 'value', 'message_to_human_agent', 'chatbot_dialog_id',
    ];
}
