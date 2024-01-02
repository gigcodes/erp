<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'plan_remarks';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
