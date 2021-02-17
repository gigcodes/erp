<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotQuestionReply extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"suggested_reply"})
     */
    protected $fillable = ['suggested_reply'];
    /**
     * @var string
     * @SWG\Property(enum={"chatbot_questions_reply"})
     */
    public $table = 'chatbot_questions_reply';

    public function storeWebsite()
    {
        return $this->belongsTo('App\StoreWebsite', 'store_website_id');
    }

}
