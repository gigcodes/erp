<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
