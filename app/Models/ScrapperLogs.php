<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapperLogs extends Model
{
    use HasFactory;

    protected $fillable = ['scrapper_id', 'task_id', 'task_type', 'log', 'created_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }
}
