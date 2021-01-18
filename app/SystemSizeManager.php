<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSizeManager extends Model
{
    protected $fillable = [
        'category_id',
        'system_size_id',
        'size',
        'status',
    ];
}
