<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErpLeadStatus extends Model
{
    //
    //use SoftDeletes;

    public $table = "erp_lead_status";

    protected $fillable = [
        'name'
    ];
}
