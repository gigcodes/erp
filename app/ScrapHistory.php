<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapHistory extends Model
{
  
    protected $fillable = [
        'operation','model','model_id','text', 'created_by',
    ];

}
