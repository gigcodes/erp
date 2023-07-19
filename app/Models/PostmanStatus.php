<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostmanStatus extends Model
{
    use HasFactory;

    public $table = 'postman_status';

    public $fillable = [
        'status_name'
    ];
}
