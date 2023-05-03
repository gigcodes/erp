<?php

namespace App;

use App\Models\GoogleDocsCategory;
use Illuminate\Database\Eloquent\Model;

class GoogleDoc extends Model
{
    public function category()
    {
        return $this->belongsTo(GoogleDocsCategory::class, 'id', 'category');
    }
}
