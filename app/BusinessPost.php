<?php

namespace App;

use App\Social\SocialConfig;
use Illuminate\Database\Eloquent\Model;

class BusinessPost extends Model
{
    protected $primaryKey = 'post_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['post_id', 'social_config_id', 'message', 'item', 'verb', 'time'];

    const FEED = 'feed';

    const STATUS = 'status';

    const PHOTO = 'photo';

    const VIDEO = 'video';

    const COMMENT = 'comment';

    const COMMENTS = 'comments';

    public function comments()
    {
        return $this->hasMany(\App\BusinessComment::class, 'post_id')->where('is_parent', 0);
    }

    public function bussiness_social_configs()
    {
        return $this->belongsTo(SocialConfig::class, 'social_config_id')->select('store_website_id', 'id', 'platform');
    }
}
