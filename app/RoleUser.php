<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';

    protected $fillable = ['user_id', 'role_id'];

    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }

}
