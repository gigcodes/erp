<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'courier', 'awb', 'l_dimension', 'h_dimension', 'w_dimension', 'weight'
  ];
}
