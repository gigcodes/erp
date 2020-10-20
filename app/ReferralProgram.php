<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferralProgram extends Model
{
    protected $fillable = ['name','uri','credit','currency','lifetime_minutes','store_website_id'];
}
