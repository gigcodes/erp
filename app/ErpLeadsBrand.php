<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErpLeadsBrand extends Model
{
    protected $fillable =[
        "brand_id",
        "erp_lead_id",
    ];
}
