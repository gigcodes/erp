<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Images extends Model
{
  use Mediable;
  
  public function tags()
  {
    return $this->belongsToMany('App\Tag', 'image_tags', 'image_id', 'tag_id');
  }
}
