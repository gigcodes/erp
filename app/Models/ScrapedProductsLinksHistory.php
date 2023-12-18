<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedProductsLinksHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'scraped_products_links_id',
        'status',
    ];
}
