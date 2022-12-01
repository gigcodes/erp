<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDiscountExcelFile extends Model
{
    protected $table = 'product_discount_excel_files';

    protected $fillable = [
        'supplier_brand_discounts_id',
        'excel_name',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function updated_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
