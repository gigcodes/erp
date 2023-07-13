<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\TechnicalFrameWork;

class TechnicalDebt extends Model
{
    use HasFactory;

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function technical_framework()
    {
        return $this->hasOne(TechnicalFrameWork::class , 'id', 'technical_framework_id');
    }
}
