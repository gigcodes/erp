<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockWebMessageList extends Model
{
    protected $fillable = [
        'object_id', 'object_type', 'created_at', 'updated_at'
    ];

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function has_posted_reviews()
    {
        $count = $this->hasMany('App\Review')->where('status', 'posted')->count();

        return $count > 0;
    }
}
