<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreDevelopmentRemark extends Model
{
    protected $fillable = ['remarks', 'store_development_id', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
