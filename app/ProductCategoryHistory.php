<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryHistory extends Model
{
    protected $fillable = ['product_id','old_category_id','category_id','user_id'];
}
