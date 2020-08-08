<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierSize extends Model
{
    protected $table = 'supplier_size';
    public $timestamps = false;
    protected $fillable = [
        'size'
    ];
}