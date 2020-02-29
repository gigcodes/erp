<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class BarcodeMedia extends Model
{
    use Mediable;
    protected $fillable = ['media_id', 'type', 'type_id', 'name', 'price', 'extra', 'created_at', 'updated_at'];
}
