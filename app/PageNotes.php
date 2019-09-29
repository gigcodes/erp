<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageNotes extends Model
{
    protected $fillable = [
        'url', 'note', 'user_id',
    ];

    public function user()
    {
    	return $this->hasOne("\App\User","id", "user_id");
    }
}
