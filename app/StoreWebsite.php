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
        'cropper_color',
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
}
