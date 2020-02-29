<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteProduct extends Model
{
	public $timestamps = false;
    //
	public $fillable = [
		"product_id",
		"store_website_id"
	];

}
