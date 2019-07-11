<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyCashFlow extends Model
{
  protected $fillable = [
    'received_from', 'paid_to', 'date', 'expected', 'received'
  ];
}
