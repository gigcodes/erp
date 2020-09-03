<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramThread extends Model
{
    public function conversation() {
        return $this->hasMany(InstagramDirectMessages::class, 'instagram_thread_id', 'id');
    }

    public function lead() {
        return $this->belongsTo(ColdLeads::class, 'cold_lead_id', 'id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id')->whereNotNull('proxy');
    }

    public function instagramUser()
    {
        return $this->hasOne(InstagramUsersList::class, 'id', 'instagram_user_id');
    
    }

    public function lastMessage()
    {
        return $this->hasOne(InstagramDirectMessages::class, 'instagram_thread_id', 'id')->orderBy('id','desc')->where('message_type',1);
    }
}
