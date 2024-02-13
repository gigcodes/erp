<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScriptsExecutionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'script_document_id',
        'description',
        'run_time',
        'run_output',
        'run_status',
    ];

    public function scriptDocument()
    {
        return $this->belongsTo(\App\Models\ScriptDocuments::class, 'script_document_id');
    }
}
