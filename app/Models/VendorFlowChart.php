<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorFlowChart extends Model
{
    use HasFactory;

    protected $fillable = ['name','created_by','sorting','master_id'];

    public function master()
    {
        return $this->belongsTo(VendorFlowChartMaster::class);
    }
}
