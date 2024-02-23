<?php

namespace App\Models;

use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteCsvPullHistory extends Model
{
    use HasFactory;

    protected $table = 'store_website_csv_pull_histories';

    public function storewebsite()
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
