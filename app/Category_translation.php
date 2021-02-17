<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Category_translation extends Model
{
    /**
     * @var string
     * @SWG\Property(enum={"category_id", "locale", "title", "site_id", "is_rejected"})
     */
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
