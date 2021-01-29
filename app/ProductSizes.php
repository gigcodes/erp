<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Supplier;

class ProductSizes extends Model
{
    //
    protected $fillable = ['product_id', 'supplier_id', 'quantity' , 'size'];

    public function supplier()
    {
        return $this->hasOne(\App\Supplier::class,'id','supplier_id');
    }
}
