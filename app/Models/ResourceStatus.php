<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceStatus extends Model
{
    use HasFactory;

    public $fillable = ['status_name', 'status_color'];
}
