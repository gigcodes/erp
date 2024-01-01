<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorFlowChartRemarks extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'flow_chart_id', 'remarks', 'added_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
