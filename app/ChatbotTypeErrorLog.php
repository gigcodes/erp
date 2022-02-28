<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotTypeErrorLog extends Model
{
    use Mediable;
    /**
     * @var string
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="chatbot_id",type="integer")
     * @SWG\Property(property="phone_number",type="string")
     * @SWG\Property(property="type_error",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = ['store_website_id', 'chatbot_id', 'phone_number', 'type_error', 'created_at', 'updated_at'];
}
