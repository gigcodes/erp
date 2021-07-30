<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentStatusHistory extends Model
{
    //
    protected $fillable = [
        "site_development_id",
        "status_id",
        "user_id",
    ];


    public function siteDevelopment()
    {
        return $this->hasOne(\App\SiteDevelopment::class,'id','site_development_id');
    }

    public function status()
    {
        return $this->hasOne(\App\SiteDevelopmentStatus::class,'id','status_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class,'id','user_id');
    }

}
