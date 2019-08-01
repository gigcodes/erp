<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloggerProductImage extends Model
{
    protected $fillable = ['file_name','blogger_product_id','other'];
}
