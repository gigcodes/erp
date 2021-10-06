<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAssign extends Model
{
    protected $table = 'email_assignes';

    protected $fillable = [
        'email_address_id',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }

    public function emailAddress()
    {
        return $this->hasOne(\App\EmailAddress::class, 'id', 'email_address_id');
    }
}
