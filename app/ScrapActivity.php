<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapActivity extends Model
{
  protected $fillable = [
    'website', 'scraped_product_id', 'status'
  ];
}
