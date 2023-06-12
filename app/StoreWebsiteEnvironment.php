<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteEnvironment extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'env_data'];

    //Tell laravel to fetch text values and set them as arrays
    protected $casts = [
        'env_data' => 'array',
    ];

    public function storeWebsite()
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}
