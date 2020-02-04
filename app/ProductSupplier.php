<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
	public $timestamps = false;
    //

	public function supplier()
	{
		return $this->hasOne("\App\Supplier","id","supplier_id");
	}
}
