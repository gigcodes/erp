<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankStatementFile extends Model
{
    protected $table = 'bank_statement_file';

    protected $fillable = [
        'filename',
        'path',
        'mapping_fields',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
