<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Images extends Model
{
  use Mediable;
  use SoftDeletes;

  public function tags()
  {
    return $this->belongsToMany('App\Tag', 'image_tags', 'image_id', 'tag_id');
  }

  public function saveFromSearchQueues($path,$link,$filename){
    if ( copy($link, $path.'/'.$filename) ) {
        return true;
    }else{
        return false;
    }
  }
}
