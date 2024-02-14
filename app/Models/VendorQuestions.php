<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorQuestions extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'question', 'sorting'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
