<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchAttachedImagesLog extends Model
{
    //
    protected $fillable = ['user_id', 'comment'];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'user_id');
    }
}
