<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstimatedDeliveryHistory extends Model
{
    protected $fillable = array(
        'order_id', 'field', 'updated_by','old_value','new_value'
    );
}
