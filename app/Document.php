<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DocumentCategory;

class Document extends Model
{
  protected $fillable = [
    'user_id', 'name', 'filename','category_id','version'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function documentCategory()
    {
    	return $this->hasOne(DocumentCategory::class,'id','category_id');
    }
}