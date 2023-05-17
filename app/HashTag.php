<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="hashtag",type="string")
     */
    protected $fillable = ['hashtag', 'platforms_id', 'instagram_account_id'];

    public function posts()
    {
        return $this->hasMany(HashtagPosts::class, 'hashtag_id', 'id');
    }

    public function instagramPost()
    {
        return $this->hasMany(InstagramPosts::class, 'hashtag_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
