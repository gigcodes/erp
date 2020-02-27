<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuggestionProduct extends Model
{
    protected $fillable = [
        'suggestion_id', 'product_id', 'created_at', 'updated_at',
    ];
}
