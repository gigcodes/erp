<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstructionTime extends Model
{
  
  protected $fillable = [
    'start', 'end', 'instructions_id', 'total_minutes'
  ];

}
