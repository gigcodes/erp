<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ImQueue;

class SocialMarketingPlatform extends Model
{
  protected $fillable = [
    'platform',
    'description',
    'status',
    'created_at',
    'updated_at'
  ];
}
