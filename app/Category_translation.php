<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_translation extends Model
{
    protected $fillable = [
        'category_id',
        'locale',
        'title',
        'site_id',
        'is_rejected'
];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function site()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'site_id');
    }
}
