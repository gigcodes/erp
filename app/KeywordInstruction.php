<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeywordInstruction extends Model
{
    protected $casts = [
        'keywords' => 'array'
    ];

    public function instruction() {
        return $this->belongsTo(InstructionCategory::class, 'instruction_category_id', 'id');
    }
}
