<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskSubject extends Model
{
    //
    public function category(){
        return $this->belongsTo(TaskSubCategory::class);
    }
    public function history(){
        return $this->hasMany(TaskHistories::class,"task_subject_id","id");
    }
}
