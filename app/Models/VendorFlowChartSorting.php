<?php

namespace App\Models;
use App\Models\VendorFlowChart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorFlowChartSorting extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id','flow_chart_id','sorting_f'];

    public function flowchart()
    {
        return $this->belongsTo(VendorFlowChart::class, 'flow_chart_id');
    }
}
