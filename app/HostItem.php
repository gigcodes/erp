<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostItem extends Model
{
    protected $table = 'host_items';

    protected $fillable = [
        'hostid', 'host_id',
    ];
}
