<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AutoReply extends Model
{
    /**
     * @var string
     * @SWG\Property(enum={"type", "keyword", "reply"})
     */
    protected $fillable = [
        'type', 'keyword', 'reply',
    ];

}
