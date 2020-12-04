<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class Newsletter extends Model
{
    protected $fillable = [
       'subject' , 'store_website_id' , 'sent_at' , 'sent_on' , 'updated_by','mail_list_id'
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

    public function mailinglist()
    {
        return $this->hasOne(\App\Mailinglist::class, 'id', 'mail_list_id');
    }

}
