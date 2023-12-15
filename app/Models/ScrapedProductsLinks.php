<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedProductsLinks extends Model
{
    use HasFactory;

    public $fillable = [
        'scrap_product_id',
        'website',
        'links',
        'status',
    ];
}
