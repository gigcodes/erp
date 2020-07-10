<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DutyGroup extends Model
{
    protected $fillable = [
        'name',
        'hs_code',
        'duty',
        'vat',
        'created_at',
        'updated_at'
    ];
}
