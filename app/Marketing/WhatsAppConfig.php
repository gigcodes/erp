<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Model;

class WhatsappConfig extends Model
{
    protected $fillable = ['number','provider','username','password','is_customer_support'];
}
