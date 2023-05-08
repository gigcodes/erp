<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class RejectedImages extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="website_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = [
        'website_id', 'product_id', 'status', 'user_id',
    ];

    public static function getRejectedMediasFromProductId($product_id)
    {
        return  \App\RejectedImages::join('mediables', 'mediables.mediable_id', 'rejected_images.product_id')->leftJoin('media', 'media.id', 'mediables.media_id')->join('store_websites', 'store_websites.id', '=', 'rejected_images.website_id')->where('product_id', $product_id)->get();
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function store_website()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'website_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
}
