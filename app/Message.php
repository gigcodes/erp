<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;


class Message extends Model
{
    use Mediable;

    protected $fillable = ['body','subject','moduletype','userid', 'customer_id', 'assigned_to','status','moduleid'];
    public function user()
	{
	   return $this->belongsTo(User::class);
	}
}
