<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleDrive extends Model
{
    use HasFactory;

    protected $table = 'google_drives';

    protected $fillable = ['date', 'user_module', 'uploaded_file', 'remarks', 'dev_task'];
}
