<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTableColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'section_name', 'column_name'
    ];
}