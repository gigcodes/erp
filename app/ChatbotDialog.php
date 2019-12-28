<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialog extends Model
{
    protected $fillable = [
        'name', 'title', 'parent_id', 'match_condition', 'workspace_id', 'previous_sibling', 'metadata'
    ];

    public function response()
    {
        return $this->hasMany("App\ChatbotDialogResponse", "chatbot_dialog_id", "id");
    }

    public function parentResponse()
    {
        return $this->hasMany("App\ChatbotDialog", "parent_id", "id");
    }

    public function singleResponse()
    {
        return $this->hasOne("App\ChatbotDialogResponse", "chatbot_dialog_id", "id");
    }

    public function getDetails($id) 
    {

    }
}
