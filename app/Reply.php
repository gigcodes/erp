<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
  use SoftDeletes;

	protected $fillable = ['category_id', 'reply', 'model'];
	protected $dates = ['deleted_at'];

  public function category() {
    return $this->belongsTo('App\ReplyCategory', 'category_id');
  }
}
