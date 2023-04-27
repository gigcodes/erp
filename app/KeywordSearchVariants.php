<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordSearchVariants extends Model
{
    use HasFactory;
    public $fillable = [
        'keyword'
    ];

    public static function list()
    {
        return self::pluck('keyword', 'id')->toArray();
    }

    public static function updateStatusIsHashtagsGeneratedKeywordVariants() {
        \DB::table('keywordSearchVariants')->where('is_hashtag_generated', 0)->update(['is_hashtag_generated' => 1]);
    }
}
