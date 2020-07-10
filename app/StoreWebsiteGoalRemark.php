<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteGoalRemark extends Model
{
    protected $fillable = [
        'remark', 'store_website_goal_id', 'created_at', 'updated_at',
    ];
}
