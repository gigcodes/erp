<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScriptDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file',
        'category',
        'usage_parameter',
        'comments',
        'author',
    ];
}
