<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteOrder extends Model
{
    protected $fillable = ['status_id', 'order_id', 'website_id'];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, "id", "website_id");
    }
}
