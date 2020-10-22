<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotDialogErrorLog extends Model
{
    protected $fillable = ['status','response'];
    public function storeWebsite()
    {
    	return $this->belongsTo("App\StoreWebsite");
    }
    public function chatbot_dialog()
    {
    	return $this->belongsTo("App\ChatbotDialog");
    }
}
