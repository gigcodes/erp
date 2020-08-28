<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessagePhrase extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $fillable = [
        'phrase', 'total', 'word_id', 'chat_id','deleted_at','deleted_by'
    ];
}
