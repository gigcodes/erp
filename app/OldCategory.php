<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldCategory extends Model
{
    protected $fillable = ['category'];

    public function old()
    {
    	return $this->belongsTo(Old::class,'id','category_id');
    }
}
