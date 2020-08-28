<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\InstagramPosts;

class InstagramPostsComments extends Model
{
    protected $fillable = ['comment_id'];

    public function nationality() {
        return $this->belongsTo(PeopleNames::class, 'people_id', 'id');
    }

    public function instagramPost()
    {
    	return $this->hasOne(InstagramPosts::class, 'id', 'instagram_post_id');
    }
}
