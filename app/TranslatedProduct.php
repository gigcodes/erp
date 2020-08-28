<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatedProduct extends Model
{
    protected $fillable = [
    'name', 'product_id','description','short_description','language_id'
  ];
}
