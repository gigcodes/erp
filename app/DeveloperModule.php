<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperModule extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'name'
  ];

  public function tasks()
  {
    return $this->hasMany('App\DeveloperTask', 'module_id');
  }
}
