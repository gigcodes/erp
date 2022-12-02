<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSetting extends Model
{
    protected $fillable = ['scope', 'scope_id', 'store_website_id', 'website_store_id', 'website_store_view_id', 'data_type', 'name', 'path', 'value', 'created_by', 'status'];

    /** Stores */
    public function storeview()
    {
        return $this->hasOne(WebsiteStoreView::class, 'id', 'scope_id');
    }

    /** Default */
    public function website()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'scope_id');
    }

    /** Websites */
    public function store()
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'scope_id');
    }

     /** Websites */
     public function fromStoreId()
     {
         return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
     }

    public function fromStoreIdwebsite()
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'website_store_id');
        //return $this->hasOne(StoreWebsname:class, 'id', 'website_store_id');
    }
}
