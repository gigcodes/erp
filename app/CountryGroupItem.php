<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryGroupItem extends Model
{
    protected $fillable = [
        'country_code',
        'country_group_id',
        'created_at',
        'updated_at',
    ];
}
