<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramDirectMessages extends Model
{
    public function getSenderUsername()
    {
    	return $this->hasOne('App\InstagramUsersList','user_id','sender_id');
    }
    

    public function getRecieverUsername()
    {
    	return $this->hasOne('App\InstagramUsersList','user_id','receiver_id');
    }
}
