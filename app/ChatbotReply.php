<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotReply extends Model
{

   /**
     * @var string
     * @SWG\Property(enum={"question" , "reply" , "chat_id" , "replied_chat_id" , "answer" , "reply_from"})
     */
    protected $fillable = [
        'question', 'reply', 'chat_id','replied_chat_id','answer','reply_from'
    ];
}
