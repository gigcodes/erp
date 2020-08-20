<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWiseLandingPageProducts extends Model
{
    protected $table = 'store_wise_landing_page_products';

    protected $fillable = ['landing_page_products_id', 'store_website_id'];

}
