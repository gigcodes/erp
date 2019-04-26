<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComplaintThread extends Model
{
  protected $fillable = [
    'complaint_id', 'thread'
  ];
}
