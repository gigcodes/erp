<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class GoogleFileTranslateHistory extends Model
{
    use HasFactory;

    public $table = 'google_file_translate_histories';

    public function user()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

}
