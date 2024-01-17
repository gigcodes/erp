<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRatingQuestions extends Model
{
    use HasFactory;

    protected $fillable = ['created_by','question', 'sorting'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
