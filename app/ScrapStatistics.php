<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapStatistics extends Model
{
    protected $fillable = ['id', 'supplier', 'type', 'url', 'description', 'brand'];
}
