<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use App\InstagramPosts;

class HashTag extends Model
{
		   /**
     * @var string
     * @SWG\Property(property="hashtag",type="string")
     */
    protected $fillable = ['hashtag'];

    public function posts()
    {
        return $this->hasMany(HashtagPosts::class, 'hashtag_id', 'id');
    }

    public function instagramPost()
    {
        return $this->hasMany(InstagramPosts::class, 'hashtag_id', 'id');
    }

}
