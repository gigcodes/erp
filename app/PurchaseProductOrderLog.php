<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseProductOrderLog extends Model
{
    //
    protected $fillable = [
        'purchase_product_order_id', 'order_products_id', 'header_name', 'replace_from', 'replace_to','created_by'
    ];
 
    public function updated_by(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }
 
    public function sop(){
        return $this->hasOne(Sop::class, 'id', 'purchase_product_order_id');
    }
}
