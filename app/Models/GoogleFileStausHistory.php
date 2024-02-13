<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleFileStausHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'google_file_translate_id',
        'updated_by_user_id',
        'old_status',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
