<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class Newsletter extends Model
{
    protected $fillable = [
       'subject' , 'store_website_id' , 'sent_at' , 'sent_on' , 'updated_by'
    ];

    public function newsletterProduct()
    {
        return $this->hasMany(\App\NewsletterProduct::class, 'newsletter_id' , 'id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'newsletter_products', 'newsletter_id', 'product_id','id','id');

    }

}
