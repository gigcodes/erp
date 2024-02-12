<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class Product_translation extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="locale",type="string")
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="site_id",type="interger")
     * @SWG\Property(property="is_rejected",type="boolean")
     */
    use Mediable;

    protected $fillable = [
        'product_id',
        'locale',
        'title',
        'description',
        'site_id',
        'is_rejected',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function site()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'site_id');
    }
}
