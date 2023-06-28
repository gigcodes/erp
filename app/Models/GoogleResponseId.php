<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleResponseId extends Model
{
    use HasFactory;
    protected $fillable = ['chatbot_question_id', 'google_response_id', 'google_dialog_account_id'];

    public function questionModal()
    {
        return $this->hasOne(\App\ChatbotQuestion::class, 'id', 'chatbot_question_id');
    }
    public function googleAccountModal()
    {
        return $this->hasOne(\App\Models\GoogleDialogAccount::class, 'id', 'google_dialog_account_id');
    }
}
