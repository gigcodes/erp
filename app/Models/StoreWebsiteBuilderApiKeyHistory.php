<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;


class StoreWebsiteBuilderApiKeyHistory extends Model
{
    use HasFactory;

    protected $table = 'store_website_builder_api_key_histories';

    protected $fillable = ['store_website_id', 'old', 'new', 'updated_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }
}
