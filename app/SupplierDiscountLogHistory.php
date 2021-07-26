<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierDiscountLogHistory extends Model
{
   
    protected $table = "supplier_discount_log_history";

    protected $fillable = [
        'id',
        'supplier_brand_discounts_id',
        'header_name',
        'user_id',
        'old_value',
        'new_value',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
