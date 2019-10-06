<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageNotes extends Model
{
    protected $fillable = [
        'url', 'category_id', 'note', 'user_id',
    ];

    public function user()
    {
    	return $this->hasOne("\App\User","id", "user_id");
    }

    public function pageNotesCategories()
    {
    	return $this->hasOne("\App\PageNotesCategories","id", "category_id");
    }
}
