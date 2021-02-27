<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ImQueue;

class WatsonWorkspace extends Model
{
    public $table = 'watson_workspace';

  protected $fillable = [
    'id', 'type', 'element_id', 'created_at', 'deleted_at'
  ];

}
