<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductQuicksellGroup extends Model
{
    protected $table = 'product_quicksell_groups';
    protected $fillable = ['quicksell_group_id','product_id'];
}
