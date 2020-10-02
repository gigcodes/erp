<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    //
    protected $fillable = ['name', 'magento_id'];

    public function storeWebsitSize()
    {
        return $this->hasMany(\App\StoreWebsiteSize::class, 'size_id' , 'id');
    }

}
