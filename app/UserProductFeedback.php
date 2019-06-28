<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProductFeedback extends Model
{
    protected $casts = [
        'content' => 'array'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
