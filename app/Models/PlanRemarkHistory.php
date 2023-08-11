<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class PlanRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'plan_remarks';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
