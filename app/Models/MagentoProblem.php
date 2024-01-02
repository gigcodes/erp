<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoProblem extends Model
{
    use HasFactory;

    public $table = 'magento_problems';

    public $fillable = [
        'user_id',
        'source',
        'test',
        'severity',
        'type',
        'error_body',
        'status',
    ];
    
}
