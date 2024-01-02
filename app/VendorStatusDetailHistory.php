<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorStatusDetailHistory extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
}
