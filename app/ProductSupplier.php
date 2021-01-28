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

    public static function getSizeSystem($productId, $supplierId)
    {
        $product = self::where("product_id",$productId)->where("supplier_id",$supplierId)->first();

        return ($product) ? $product->size_system : "";
    }
}
