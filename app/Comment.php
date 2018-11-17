<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected  $fillable = [
    	'content',
	    'subject_type',
	    'subject_id',
	    'user_id'
    ];

    public function user(){
    	return $this->belongsTo(User::class,'user_id','id');
    }
}
