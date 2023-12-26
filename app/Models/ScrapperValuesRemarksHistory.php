<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapperValuesRemarksHistory extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'column_name', 'remarks',  'updated_by'];
}
