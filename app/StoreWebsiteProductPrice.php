<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductPrice extends Model
{
    /**
     * @var string

     *
     * @SWG\Property(property="name",type="string")
     */
    protected $appends = [
        'web_store_name',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function store_website()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }

    public function getWebStoreNameAttribute()
    {
        $p = \App\CustomerCharity::where('product_id', $this->product_id)->first();
        if ($p) {
            $webStore = \App\CharityProductStoreWebsite::find($this->web_store_id);

            return $webStore->id;
        } else {
            $webStore = \App\Website::find($this->web_store_id);

            return $webStore->name;
        }
    }
}
