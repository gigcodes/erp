<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class StoreWebsiteProductScreenshot extends Model
{

    use Mediable;

    protected $fillable = [
        'id',
        'store_website_id',
        'status',
        'product_id',
        'sku',
        'store_website_name',
        'image_path',
        'created_at',
        'updated_at',
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class,'id','store_website_id');
    }
}
