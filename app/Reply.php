<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="reply",type="string")
     * @SWG\Property(property="model",type="string")
     * @SWG\Property(property="deleted_at",type="datetime")
     */
    use SoftDeletes;

    protected $fillable = ['category_id', 'store_website_id', 'reply', 'model', 'push_to_watson', 'pushed_to_google'];

    public function category()
    {
        return $this->belongsTo(\App\ReplyCategory::class, 'category_id');
    }

    public function transalates()
    {
        return $this->hasMany(\App\TranslateReplies::class, 'replies_id', 'id')->select('translate_replies.status as translate_status', 'translate_replies.replies_id as replies_id', 'translate_replies.id as translate_id', 'translate_replies.translate_from', 'translate_replies.translate_to as translate_lang', 'translate_replies.translate_text', 'translate_replies.created_at', 'translate_replies.updated_at')->orderBy('translate_replies.id', 'ASC');
    }
}
