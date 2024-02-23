<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFlowChartMaster extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function flow_charts()
    {
        return $this->hasMany(VendorFlowChart::class, 'master_id', 'id');
    }
}
