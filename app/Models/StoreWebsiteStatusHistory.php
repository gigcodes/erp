<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\StoreWebsiteStatus;

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
