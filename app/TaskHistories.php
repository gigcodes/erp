<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskHistories extends Model
{
    //
    public function subject(){
        return $this->belongsTo(TaskSubject::class);
    }
}
