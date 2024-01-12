<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevOppsSubCategoryDocument extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'description', 'created_by', 'created_at', 'devoops_task_id'];

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
