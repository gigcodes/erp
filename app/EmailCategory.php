<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailCategory extends Model
{
    protected $table = 'email_category';
    protected $fillable = ['category_name'];
}
