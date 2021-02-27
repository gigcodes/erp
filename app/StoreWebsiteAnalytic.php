<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteAnalytic extends Model {

    protected $table = 'store_website_analytics';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'website', 'account_id', 'view_id', 'store_website_id', 'created_at', 'updated_at'];

    public function storeWebsiteDetails() {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id', 'id');
    }

}
