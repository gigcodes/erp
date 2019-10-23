<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;

class ProductQuicksellGroup extends Model
{
    protected $table = 'product_quicksell_groups';
    protected $fillable = ['quicksell_group_id','product_id'];

    public function products()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }

    

}
