<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleLanguageConstant extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_language_constant_id',
        'name',
        'code',
        'is_targetable',
    ];
}
