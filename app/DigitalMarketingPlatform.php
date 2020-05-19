<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DigitalMarketingPlatform extends Model
{

	CONST STATUS = [
		0 => "Draft",
		1 => "Active",
		2 => "Inactive",
		3 => "Planned",
		4 => "Do not need"
 	];


  protected $fillable = [
    'platform',
    'description',
    'status',
    'created_at',
    'updated_at'
  ];
}
