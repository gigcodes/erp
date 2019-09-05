<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    protected $fillable = [
    'user_id','document_id','name', 'filename','category_id','version'
  ];
}
