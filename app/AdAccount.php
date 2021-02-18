<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdAccount extends Model
{
    protected $fillable = [
        'account_name', 'note', 'config_file', 'status',
    ];
}
