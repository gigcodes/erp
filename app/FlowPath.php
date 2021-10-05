<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowPath extends Model
{
    protected $table = 'flow_paths';
    protected $fillable = [
		'flow_id',
		'deleted',
	];
}
