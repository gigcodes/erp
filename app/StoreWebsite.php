<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreWebsite extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title', 
        'remote_software',
        'website',
        'description',
        'is_published',
        'deleted_at',
        'created_at',
        'updated_at',
        'magento_url',
        'magento_username',
        'magento_password',
        'api_token',
        'cropper_color',
        'cropping_size',
        'instagram',
        'instagram_remarks',
        'facebook',
        'facebook_remarks',
        'server_ip',
        'username',
        'password',
        'staging_username',
        'staging_password',
        'mysql_username',
        'mysql_password',
        'mysql_staging_username',
        'mysql_staging_password',
        'website_source',
        'push_web_key',
        'push_web_id',
        'icon',
        'is_price_override'
    ];

    // Append attributes
    protected $appends = ['website_url'];

    public static function list()
    {
        return self::pluck("website","id")->toArray();
    }

    /**
     * Get proper website url
     */
    public function getWebsiteUrlAttribute()
    {
        $url = $this->website;
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            return $urlStr = 'http://' . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * Get store brand
     */
    public function brands()
    {
        return $this->belongsToMany('App\Brand', 'store_website_brands', 'store_website_id', 'brand_id');
    }

    /**
     * Get store categories
     */
    public function categories()
    {
        return $this->belongsToMany('App\Category', 'store_website_categories', 'store_website_id', 'category_id');
    }

    public function sizeCategory()
    {
        return $this->belongsToMany('App\Category', 'brand_category_size_charts', 'store_website_id', 'category_id');
    }

    public function sizeBrand()
    {
        return $this->belongsToMany('App\Brand', 'brand_category_size_charts', 'store_website_id', 'brand_id');
    }

    public static function shopifyWebsite()
    {
        return self::where("website_source","shopify")->pluck("title","id")->toArray();
    }

    public function websites()
    {
        return $this->hasMany('App\Website','store_website_id','id');
    }
}
