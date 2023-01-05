<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoCommand extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function website()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'website_ids');
    }
}
