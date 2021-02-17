<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotKeywordValueTypes extends Model
{
    public $timestamps = false;
    /**
     * @var string
     * @SWG\Property(enum={"type", "chatbot_keyword_value_id"})
     */
    protected $fillable = [
        'type', 'chatbot_keyword_value_id'
    ];
}
