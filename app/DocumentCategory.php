<?php

namespace App;

use App\Documents;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $fillable = array('name');

    public function documents()
    {
     return $this->belongsTo(Documents::class,'id','category_id');
    }

}

