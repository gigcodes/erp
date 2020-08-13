<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCredential extends Model
{
    protected $table = 'twilio_credentials';

    protected $fillable = ['twilio_email', 'account_id', 'auth_token'];

    public function numbers()
    {
        return $this->hasMany('App\TwilioActiveNumber', 'account_sid', 'account_id');
    }

}
