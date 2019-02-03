<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapedProducts extends Model
{
    protected $casts = [
        'images' => 'array',
        'properties' => 'array',
    ];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }
}
