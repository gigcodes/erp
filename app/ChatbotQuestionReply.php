<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestionReply extends Model
{
    protected $fillable = ['suggested_reply'];
    public $table = 'chatbot_questions_reply';

    public function storeWebsite()
    {
        return $this->belongsTo('App\StoreWebsite', 'store_website_id');
    }

}
