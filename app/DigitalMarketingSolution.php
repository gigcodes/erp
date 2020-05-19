<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DigitalMarketingSolution extends Model
{

  public $timestamps = false;

  protected $fillable = [
    'provider',
    'website',
    'contact'
  ];

}
