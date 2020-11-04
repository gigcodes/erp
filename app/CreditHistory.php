<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    protected $table='credit_history';
    protected $fillable=['customer_id','model_id','model_type','used_credit','used_in','type'];
}
