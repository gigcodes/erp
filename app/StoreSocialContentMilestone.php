<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialContentMilestone extends Model
{
    protected $fillable = [
        'task_id','ono_of_content','store_social_content_id','status'
    ];

    public function task() {
        return $this->belongsTo('App\Task','task_id','id');
    }
}
