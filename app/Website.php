<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
          /**
     * @var string
      * @SWG\Property(property="name",type="string")
      * @SWG\Property(property="code",type="string")
      * @SWG\Property(property="sort_order",type="string")
      * @SWG\Property(property="platform_id",type="integer")
      * @SWG\Property(property="order_status_id",type="integer")
      * @SWG\Property(property="is_finished",type="boolean")
     */
    protected $fillable = [
        'name', 
        'code', 
        'sort_order', 
        'platform_id', 
        'store_website_id', 
        'is_finished'
    ];

    public function stores()
    {
        return $this->hasMany(\App\WebsiteStore::class, 'website_id', 'id');
    }

    public function storesViews()
    {
        return $this->hasMany(\App\WebsiteStoreView::class, 'id', 'store_website_id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }
}
