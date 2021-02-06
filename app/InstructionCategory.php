<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstructionCategory extends Model
{
  protected $fillable = ['name'];

  public function instructions()
  {
    return $this->hasMany('App\Instruction', 'category_id');
  }
}
