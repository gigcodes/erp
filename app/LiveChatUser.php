<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

class LiveChatUser extends Model
{
    protected $fillable = ['user_id'];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
