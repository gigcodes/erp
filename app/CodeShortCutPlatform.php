<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodeShortCutPlatform extends Model
{
    public $table = 'code_shortcuts_platforms';

    protected $fillable = [
        'name',
    ];

    public function code_shortcuts()
    {
        return $this->belongsTo(CodeShortcut::class);
    }
}
