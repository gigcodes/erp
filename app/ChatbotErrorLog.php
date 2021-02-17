<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotErrorLog extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"status", "response"})
     */
    protected $fillable = ['status','response'];
    public function storeWebsite()
    {
    	return $this->belongsTo("App\StoreWebsite");
    }
}
