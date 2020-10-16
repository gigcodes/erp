<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotErrorLog extends Model
{
    protected $fillable = ['status','response'];
    public function storeWebsite()
    {
    	return $this->belongsTo("App\StoreWebsite");
    }
}
