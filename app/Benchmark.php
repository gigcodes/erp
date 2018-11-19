<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    protected $fillable = [
    	'selections',
	    'searches',
	    'attributes',
	    'supervisor',
	    'imagecropper',
	    'lister',
	    'approver',
	    'inventory',
	    'for_date'
    ];
}
