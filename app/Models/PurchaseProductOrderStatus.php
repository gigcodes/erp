<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProductOrderStatus extends Model
{
    use HasFactory;

    public $fillable = [
        'status_name',
        'status_alias',
        'status_color',
    ];
}
