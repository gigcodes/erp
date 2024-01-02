<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialContactThread extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const SEND = 1;

    const RECEIVE = 2;

    protected $fillable = ['social_contact_id', 'sender_id', 'recipient_id',  'message_id', 'text', 'type', 'sending_at'];

    public function socialContact()
    {
        return $this->belongsTo(\App\SocialContact::class);
    }
}
