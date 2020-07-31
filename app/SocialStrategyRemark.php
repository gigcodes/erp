<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialStrategyRemark extends Model
{
    protected $fillable = ['remarks','user_id','social_strategy_id'];
}
