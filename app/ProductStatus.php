<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStatus extends Model
{
    protected $table = 'product_status';
    protected $fillable = [ 'product_id', 'name', 'value' ];

    public static function updateStatus( $productId, $status, $value )
    {
        // Update first or create new status
        return self::firstOrCreate(
            [ 'product_id' => $productId, 'name' => $status ],
            [ 'value' => $value ]
        );
    }

    public function product()
    {
        return $this->belongsTo( Product::class, 'product_id', 'id' );
    }

    public static function pushRecord($product_id, $name, $value = 1) 
    {

        // save to product status history 
       $productStatus = self::where("product_id",$product_id)
       ->where("name",$name)
       ->first();

       if(!$productStatus) {
          $productStatus = new self;
       }

       $productStatus->product_id = $product_id; 
       $productStatus->name = $name;
       $productStatus->value = $value;
       $productStatus->save();
       // end for save product status history
    }
}
