<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteEnvironmentHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'key', 'old_value', 'new_value'];

    public function storeWebsite()
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}
