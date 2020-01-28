<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreWebsite extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title', 'remote_software', 'website', 'description', 'is_published', 'deleted_at', 'created_at', 'updated_at',
    ];
}
