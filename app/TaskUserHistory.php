<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskUserHistory extends Model
{
    protected $fillable = [
        'model','model_id','old_id','new_id','user_type','updated_by'];
}
