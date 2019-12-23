<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialog extends Model
{
    protected $fillable = [
        'name', 'title', 'parent_id', 'match_condition','workspace_id',
    ];

    public function response()
    {
        return $this->hasMany("App\ChatbotDialogResponse", "chatbot_dialog_id", "id");
    }
}
