<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'store_website_page_status_histories';

    public function newstatus()
    {
        return $this->belongsTo(StoreWebsiteStatus::class, 'new_status_id');
    }

    public function oldstatus()
    {
        return $this->belongsTo(StoreWebsiteStatus::class, 'old_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
