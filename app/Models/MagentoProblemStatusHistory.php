<?php

namespace App\Models;

use App\User;
use App\Models\MagentoProblemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoProblemStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['magento_problem_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(MagentoProblemStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(MagentoProblemStatus::class, 'old_value');
    }
}
