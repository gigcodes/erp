<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSizeManager extends Model
{
    protected $fillable = [
        'category_id',
        'erp_size',
        'status',
    ];
}
