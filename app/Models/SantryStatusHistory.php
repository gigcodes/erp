<?php

namespace App\Models;
use App\User;
use App\Models\SentyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SantryStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['santry_log_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(SentyStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(SentyStatus::class, 'old_value');
    }
}
