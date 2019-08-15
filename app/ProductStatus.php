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
}
