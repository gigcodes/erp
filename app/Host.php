<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $table = 'hosts'; 
    protected $fillable = [
        'hostid', 'host', 'name',
    ];
}
