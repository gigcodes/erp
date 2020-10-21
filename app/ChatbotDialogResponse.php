<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialogResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'response_type', 'value', 'message_to_human_agent', 'chatbot_dialog_id',
    ];

    public function dialog(){
        return $this->belongsTo(ChatbotDialog::class, 'chatbot_dialog_id', 'id');
    }
    public function storeWebsite(){
        return $this->belongsTo(StoreWebsite::class, 'store_website_id', 'id');
    }
}
