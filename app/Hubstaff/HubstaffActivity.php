<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivity extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'starts_at',
        'tracked'
    ];

        
}
