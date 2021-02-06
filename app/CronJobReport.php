<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronJobReport extends Model
{
  protected $fillable = [
    'signature', 'start_time', 'end_time'
  ];
}
