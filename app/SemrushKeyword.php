<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SemrushKeyword extends Model
{
    protected $table = 'semrush_keywords';

    protected $fillable = ['keyword'];
}
