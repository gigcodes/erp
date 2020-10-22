<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleFiletranslatorFile extends Model
{
    protected $table = 'googlefiletranslatorfiles';
    protected $fillable = ['name','tolanguage'];
}
