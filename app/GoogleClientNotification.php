<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleClientNotification extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }

    public function account()
    {
        return $this->hasOne(GoogleClientAccountMail::class, 'id', 'google_client_id');
    }
}
