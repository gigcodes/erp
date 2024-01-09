<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapStatisticsStaus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'status_value'
    ];
}
