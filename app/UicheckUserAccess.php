<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckUserAccess extends Model
{
    protected $fillable = ['user_id', 'uicheck_id'];
}
