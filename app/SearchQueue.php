<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchQueue extends Model
{
    protected $table = 'search_queues'; 
	
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
