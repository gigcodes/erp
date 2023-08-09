<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicalDebt extends Model
{
    use HasFactory;

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function technical_framework()
    {
        return $this->hasOne(TechnicalFrameWork::class, 'id', 'technical_framework_id');
    }
}
