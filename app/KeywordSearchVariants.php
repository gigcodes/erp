<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeywordSearchVariants extends Model
{
    use HasFactory;

    public $fillable = [
        'keyword',
    ];

    public static function list()
    {
        return self::pluck('keyword', 'id')->toArray();
    }
}
