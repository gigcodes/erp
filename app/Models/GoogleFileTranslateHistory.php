<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleFileTranslateHistory extends Model
{
    use HasFactory;

    public $table = 'google_file_translate_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
