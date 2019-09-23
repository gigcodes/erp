<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    protected $fillable = [
       'password_id','website', 'url', 'username', 'password' , 'registered_with'
    ];



}
