<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DutyGroupCountry extends Model
{
    protected $fillable = [
        'duty_group_id',
        'country_duty_id',
        'created_at',
        'updated_at'
    ];
}
