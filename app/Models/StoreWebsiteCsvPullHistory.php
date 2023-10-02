<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
use App\User;


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
