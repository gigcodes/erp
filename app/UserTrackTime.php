<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTrackTime extends Model
{
    protected $fillable = ['id','user_id','user_name','hubstaff_tracked_hours','hours_tracked_with','hours_tracked_without', 'task_id', 'approved_hours', 'difference_hours', 'total_hours', 'activity_levels', 'status', 'created_at', 'update_at'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}