<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoProblemUserHistory extends Model
{
    use HasFactory;

    protected $fillable = ['magento_problem_id', 'old_value', 'new_value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(User::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(User::class, 'old_value');
    }
}
