<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotKeywordValue extends Model
{
	public $timestamps = false;
	/**
     * @var string
     * @SWG\Property(enum={"value", "chatbot_keyword_id", "types"})
     */
    protected $fillable = [
        'value', 'chatbot_keyword_id','types'
    ];

    public function chatbotKeywordValueTypes() {
        return $this->hasMany("App\ChatbotKeywordValueTypes", "chatbot_keyword_value_id", "id");
    }
}
