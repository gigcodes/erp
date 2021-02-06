<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class StoreWebsiteColor extends Model
{


    public $fillable = [ 'id','store_website_id', 'store_color', 'erp_color'];

    /**
     * Get store categories
     */
    public function storeWebsite()
    {
        return $this->belongsTo('App\StoreWebsite');
    }

}
