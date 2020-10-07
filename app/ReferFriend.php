<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferFriend extends Model
{
    protected $fillable = [
    'referrer_first_name',
    'referrer_last_name',
    'referrer_email',
    'referrer_phone',
    'referee_first_name',
    'referee_last_name',
    'referee_email',
    'referee_phone',
    'domain_name'];
    protected $table='refer_friend';
}
