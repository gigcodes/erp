<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleServer extends Model
{
    public $table = 'google_server';
    
    protected $fillable = [
        'name',
        'key',
        'description',
        'created_at',
        'updated_at',
    ];

}
