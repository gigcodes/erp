<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperCost extends Model
{
  protected $fillable = [
    'user_id', 'amount', 'paid_date'
  ];
}
