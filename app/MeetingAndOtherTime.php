<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingAndOtherTime extends Model
{
    protected $fillable = [
        'model','model_id','user_id','time','type','note','old_time','approve','updated_by'];
}
