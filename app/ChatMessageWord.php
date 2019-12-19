<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessageWord extends Model
{
    protected $fillable = [
        'word', 'total',
    ];

    public function pharases()
    {
    	return $this->hasMany('App\ChatMessagePhrase','word_id','id');
    }
}
