<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\HashTag;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Affiliates extends Model
{
    /**
     * @var string
     * @SWG\Property(enum={"location","hashtag_id","location","caption","posted_at","source","address","facebook","facebook_followers","instagram","instagram_followers","twitter","twitter_followers","youtube","youtube_followers","linkedin","linkedin_followers","pinterest","pinterest_followers","phone","emailaddress","title","is_flagged","first_name","last_name","url","website_name","unique_visitors_per_month","page_views_per_month","worked_on","city","postcode","country","type","store_website_id"})
     */
    protected $fillable = [
        'location',
        'hashtag_id',
        'location',
        'caption',
        'posted_at',
        'source',
        'address',
        'facebook',
        'facebook_followers',
        'instagram',
        'instagram_followers',
        'twitter',
        'twitter_followers',
        'youtube',
        'youtube_followers',
        'linkedin',
        'linkedin_followers',
        'pinterest',
        'pinterest_followers',
        'phone',
        'emailaddress',
        'title',
        'is_flagged',
        'first_name',
        'last_name',
        'url',
        'website_name',
        'unique_visitors_per_month',
        'page_views_per_month',
        'worked_on',
        'city',
        'postcode',
        'country',
        'type',
        'store_website_id'
    ];

    public function hashTags()
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }
}
