<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBox extends Model
{
    use HasFactory;

    protected $fillable = ['box_name'];
}
