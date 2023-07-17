<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariable extends Model
{
    use HasFactory;

    public $fillable = [
        'project_id',
        'filename',
        'file_path',
        'variable',
        'value',
        'create_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

}
