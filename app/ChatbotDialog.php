<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialog extends Model
{
    protected $fillable = [
        'name', 'title', 'parent_id', 'match_condition', 'workspace_id', 'previous_sibling', 'metadata',
    ];

    public function response()
    {
        return $this->hasMany("App\ChatbotDialogResponse", "chatbot_dialog_id", "id");
    }

    public function parentResponse()
    {
        return $this->hasMany("App\ChatbotDialog", "parent_id", "id");
    }

    public function previous()
    {
        return $this->hasOne("App\ChatbotDialog", "id", "previous_sibling");
    }

    public function parent()
    {
        return $this->hasOne("App\ChatbotDialog", "id", "parent_id");
    }

    public function singleResponse()
    {
        return $this->hasOne("App\ChatbotDialogResponse", "chatbot_dialog_id", "id");
    }

    public function getPreviousSiblingName()
    {
        return ($this->previous) ? $this->previous->name : null;
    }

    public function getParentName()
    {
        return ($this->parent) ? $this->parent->name : null;
    }

    public function multipleCondition()
    {
        return $this->hasMany("App\ChatbotDialog", "parent_id", "id");
    }

}
