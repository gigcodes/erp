<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreMasterStatus extends Model
{
    protected $fillable = [
        'store_website_id','value','label'
    ];
}
