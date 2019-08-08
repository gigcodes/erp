<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperMessagesAlertSchedules extends Model
{
    protected $casts = [
        'time' => 'array'
    ];
}
