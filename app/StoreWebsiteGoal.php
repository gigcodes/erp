<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteGoal extends Model
{
    protected $fillable = [
        'goal', 'solution', 'store_website_id', 'created_at', 'updated_at',
    ];

    public function remarks()
    {
    	return $this->hasMany("App\StoreWebsiteGoalRemark","store_website_goal_id","id");
    }
}
