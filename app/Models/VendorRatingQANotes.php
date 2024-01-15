<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRatingQANotes extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'question_id', 'notes',  'user_id'];
}
