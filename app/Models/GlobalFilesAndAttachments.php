<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalFilesAndAttachments extends Model
{
    public $fillable = [
        'id',
        'module_id',
        'module',
        'title',
        'filename',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }
}
