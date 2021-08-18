<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSetting extends Model
{

    protected $fillable = ['scope','scope_id','name','path', 'value'];

    /** Stores */
    public function storeview(){
        return $this->hasOne(WebsiteStoreView::class, 'id', 'scope_id');
    }

    /** Default */
    public function website(){
        return $this->hasOne(StoreWebsite::class, 'id', 'scope_id');
    }

    /** Websites */
    public function store(){
        return $this->hasOne(WebsiteStore::class, 'id', 'scope_id');
    }

}
