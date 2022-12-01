<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushToMagentoCondition extends Model
{
    protected $table = 'push_to_magento_conditions';

    protected $fillable = [
        'condition',
        'description',
        'status',
        'upteam_status',
    ];
}
