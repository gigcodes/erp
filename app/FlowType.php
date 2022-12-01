<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowType extends Model
{
    protected $table = 'flow_types';

    protected $fillable = [
        'type',
        'deleted',
    ];
}
