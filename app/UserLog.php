<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

class UserLog extends Model
{
    protected $fillable = ['user_id','url','user_name'];

    public function users(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function scopeBetween($query, Carbon $from, Carbon $to)
    {
        $query->whereBetween('created_at', [$from, $to]);
    }

}
