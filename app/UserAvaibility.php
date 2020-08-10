<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAvaibility extends Model
{
    protected $fillable = ['user_id','from','to','status','note','date'];
}
