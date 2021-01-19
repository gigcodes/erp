<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSizeRelation extends Model
{
    protected $fillable = [
        'system_size_manager_id',
        'system_size',
        'size',
    ];
}
