<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
  public function tags()
  {
    return $this->belongsToMany('App\Tag', 'image_tags', 'image_id', 'tag_id');
  }
}
