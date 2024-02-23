<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOopsStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['devoops_sub_category_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(DevOopsStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(DevOopsStatus::class, 'old_value');
    }
}
