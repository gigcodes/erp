<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeReplacement extends Model
{
    public function user() {
        return $this->belongsTo(User::class, 'authorized_by', 'id');
    }
}
