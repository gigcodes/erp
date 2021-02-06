<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstaMessages extends Model
{
    protected $fillable = ['number', 'message', 'lead_id', 'order_id', 'approved', 'status', 'media_url'];
}
