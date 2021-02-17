<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatMessagesQuickData extends Model
{
    protected $table = "chat_messages_quick_datas";
    /**
     * @var string
     * @SWG\Property(property="model",type="string")
     */
    protected $fillable = ['model', 'model_id', 'last_unread_message', 'last_unread_message_at', 'last_communicated_message', 'last_communicated_message_at','last_unread_message_id','last_communicated_message_id'];
    protected $dates = ['created_at', 'updated_at'];

}
