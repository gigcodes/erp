<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

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
        return $this->belongsTo(User::class,'updated_by_user_id');
    }
}
