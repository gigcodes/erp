<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $table = 'sales_item';

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'category' => 'array'
    ];
}
