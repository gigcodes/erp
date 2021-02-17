<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatMessagePhrase extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    /**
     * @var string
     * @SWG\Property(enum={ "phrase", "total", "word_id", "chat_id","deleted_at","deleted_by"})
     */

    protected $fillable = [
        'phrase', 'total', 'word_id', 'chat_id','deleted_at','deleted_by'
    ];
}
