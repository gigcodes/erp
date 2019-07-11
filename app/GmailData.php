<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GmailData extends Model
{
    protected $table = 'gmail_data';

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
    ];
}
