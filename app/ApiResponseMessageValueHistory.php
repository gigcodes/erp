<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiResponseMessageValueHistory extends Model
{
    protected $table = 'api_response_message_value_histories';

    public function User()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }
}
