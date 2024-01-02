<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function supplier()
    {
        return $this->hasOne(\App\Supplier::class, 'id', 'supplier_id');
    }

    public function product()
    {
        return $this->hasOne(\App\Product::class, 'id', 'product_id');
    }

    public static function getSizeSystem($productId, $supplierId)
    {
        $product = self::where('product_id', $productId)->where('supplier_id', $supplierId)->first();

        return ($product) ? $product->size_system : '';
    }
}
