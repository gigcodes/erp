<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoProblemUserHistory extends Model
{
    use HasFactory;

    protected $fillable = ['magento_problem_id', 'old_value', 'new_value',  'user_id'];
}
