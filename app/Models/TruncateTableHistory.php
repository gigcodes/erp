<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruncateTableHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
