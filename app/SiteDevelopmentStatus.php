<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentStatus extends Model
{
    protected $fillable = [
        'name', 'created_at', 'updated_at',
    ];
}
