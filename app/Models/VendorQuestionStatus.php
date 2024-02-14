<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorQuestionStatus extends Model
{
    use HasFactory;

    public $fillable = [
        'status_name',
        'status_color',
    ];
}
