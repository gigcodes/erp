<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id','vendor_id','answer'];
}
