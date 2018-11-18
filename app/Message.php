<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Message extends Model
{
    //

    protected $fillable = ['body','subject','moduletype','userid','status','moduleid'];
    public function user()
	{
	   return $this->belongsTo(User::class);
	}
}
