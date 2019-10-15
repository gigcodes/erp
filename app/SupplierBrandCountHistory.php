<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierBrandCountHistory extends Model
{
    protected $table = 'supplier_brand_count_histories';

    protected $fillable = [ 'supplier_id', 'brand_id', 'cnt','url','category_id' , 'supplier_brand_count_id'];
}
