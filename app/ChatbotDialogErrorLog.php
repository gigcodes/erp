<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotDialogErrorLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="response",type="string")
     */
    protected $fillable = ['status', 'response', 'reply_id', 'request'];

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }

    public function chatbot_dialog()
    {
        return $this->belongsTo(\App\ChatbotDialog::class);
    }
}
