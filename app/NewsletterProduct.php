<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class NewsletterProduct extends Model
{
    protected $fillable = [
       'product_id' , 'newsletter_id'
    ];

    public function product()
    {
      return $this->hasOne(\App\Product::class, 'id' , 'product_id');
    }
}
