<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuColorReferences extends Model
{
    protected $fillable = [ 'brand_id', 'color_name', 'color_code'];
}