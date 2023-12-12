<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapperValues extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'task_type', 'scrapper_values',  'added_by'];
}