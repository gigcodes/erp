<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierInventory extends Model
{
    protected $table = 'supplier_inventory';
    protected $fillable = [ 'supplier', 'sku', 'inventory' ];

}
