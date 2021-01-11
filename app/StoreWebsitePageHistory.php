<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsitePageHistory extends Model
{
    protected $fillable = [
        'content','url','content','result','result_type', 'store_website_page_id', 'updated_by', 'created_at', 'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
