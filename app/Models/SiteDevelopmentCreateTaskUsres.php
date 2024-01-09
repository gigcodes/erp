<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentCreateTaskUsres extends Model
{
    use HasFactory;

    protected $fillable = ['user_ids'];
}
