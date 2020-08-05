<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierSubCategory extends Model
{
    protected $table = 'supplier_subcategory';
    public $timestamps = false;
    protected $fillable = [
        'name'
    ];
}